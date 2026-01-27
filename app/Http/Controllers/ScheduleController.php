<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtenemos los horarios del usuario autenticado
        $query = Schedule::where('user_id', Auth::id());

        /**
         * 2. OPTIONAL: FullCalendar envía automáticamente los parámetros 
         * 'start' y 'end' cuando cambias de mes/semana. 
         * Podemos filtrar por fecha para que la carga sea más rápida.
         */
        if ($request->has(['start', 'end'])) {
            $query->whereBetween('date', [
                substr($request->start, 0, 10),
                substr($request->end, 0, 10)
            ]);
        }

        $schedules = $query->get();

        // 3. Retornamos el JSON. 
        // Laravel incluirá automáticamente 'start', 'end', 'title' y 'backgroundColor'
        // gracias a que los definimos en el Modelo.
        return response()->json($schedules);
    }

    /**
     * Ejemplo para crear un horario nuevo (POST)
     */
    public function store(Request $request)
    {
        // 1. Validación estricta
        $validated = $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        // 2. Crear el horario
        $schedule = Schedule::create([
            'user_id'     => Auth::id(),
            'date'        => $validated['date'],
            'start_time'  => $validated['start_time'],
            'end_time'    => $validated['end_time'],
            'is_available' => true,
        ]);

        // 3. Formatear para FullCalendar
        $event = [
            'id'             => $schedule->id,
            'title'          => 'Disponible',
            'start'          => $schedule->date . 'T' . $schedule->start_time,
            'end'            => $schedule->date . 'T' . $schedule->end_time,
            'backgroundColor' => '#22c55e',
            'borderColor'    => '#16a34a',
            'is_available'   => true,
        ];

        return response()->json($event, 201);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $schedule->update([
            'is_available' => $validated['is_available'],
            'is_blocked'   => !$validated['is_available'],

        ]);

        return response()->json([
            'id' => $schedule->id,
            'title' => $schedule->is_available ? 'Disponible' : 'No disponible',
            'start' => $schedule->date . 'T' . $schedule->start_time,
            'end' => $schedule->date . 'T' . $schedule->end_time,
            'is_available' => $schedule->is_available,
            'backgroundColor' => $schedule->is_available ? '#22c55e' : '#ef4444',
            'borderColor' => $schedule->is_available ? '#16a34a' : '#dc2626',
        ]);
    }

    /**
     * Generar horarios en un rango de fechas y horas
     */
    public function generateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'user_id'    => 'required|exists:users,id',
            'allow_past' => 'sometimes|boolean',
        ]);

        $allowPast = $request->boolean('allow_past', false);

        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        $endDate   = Carbon::createFromFormat('Y-m-d', $request->end_date)->startOfDay();

        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime   = Carbon::createFromFormat('H:i', $request->end_time);

        $createdOrUpdated = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Comparamos una copia para NO mutar $date
            if (! $allowPast) {
                if ($date->copy()->endOfDay()->lt(Carbon::now())) {
                    continue;
                }
            }

            // Construimos slots: slotStart = date + start_time, slotEnd = slotStart + 30min
            $slotStart = $date->copy()->setTimeFrom($startTime);
            $slotEnd   = $slotStart->copy()->addMinutes(30);

            // Queremos incluir el slot cuyo end == end_time, por eso comparamos slotEnd <= end_time
            $dayEndLimit = $date->copy()->setTimeFrom($endTime);

            while ($slotEnd->lte($dayEndLimit)) {
                $schedule = Schedule::where('user_id', $request->user_id)
                    ->where('date', $date->toDateString())
                    ->where('start_time', $slotStart->format('H:i:s'))
                    ->where('end_time', $slotEnd->format('H:i:s'))
                    ->first();

                if ($schedule) {
                    $schedule->update(['is_available' => true]);
                } else {
                    $schedule = Schedule::create([
                        'user_id'      => $request->user_id,
                        'date'         => $date->toDateString(),
                        'start_time'   => $slotStart->format('H:i:s'),
                        'end_time'     => $slotEnd->format('H:i:s'),
                        'is_available' => true,
                    ]);
                }

                $createdOrUpdated[] = $schedule;

                $slotStart->addMinutes(30);
                $slotEnd->addMinutes(30);
            }
        }

        return response()->json([
            'message' => 'Horarios generados/actualizados correctamente',
            'schedules' => $createdOrUpdated
        ], 201);
    }

    public function available()
    {
        $schedules = Schedule::where('is_available', true)
            ->where('is_blocked', false)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return $schedules->map(function ($s) {
            $date = substr($s->date, 0, 10); // ← FIX

            return [
                'id' => $s->id,
                'date' => $date,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'start' => $date . 'T' . $s->start_time, // ← FORMATO CORRECTO
                'end'   => $date . 'T' . $s->end_time,   // ← FORMATO CORRECTO
                'is_available' => $s->is_available,
            ];
        });
    }
}
