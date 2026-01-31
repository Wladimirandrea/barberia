<?php

namespace App\Http\Controllers;

use App\Models\DaysOff;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DaysOffController extends Controller
{
    /**
     * Devuelve la configuración actual del usuario.
     */
    public function show(Request $request)
    {
        $daysOff = DaysOff::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'monday'    => false,
                'tuesday'   => false,
                'wednesday' => false,
                'thursday'  => false,
                'friday'    => false,
                'saturday'  => false,
                'sunday'    => false
            ]
        );
        return response()->json($daysOff);
    }

    /**
     * Toggle puntual: { day: 'monday', value: true }
     * value = true  => Es día libre (No disponible)
     * value = false => Se trabaja (Disponible)
     */

    public function toggle(Request $request)
{
    $payload = $request->only(['day', 'value']);

    $validator = Validator::make($payload, [
        'day'   => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        'value' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $dayName = strtolower($payload['day']);
    $isDayOff = (bool) $payload['value']; // Aseguramos booleano puro
    $user = $request->user();

    // Mapa MySQL WEEKDAY() style (0 = lunes, 6 = domingo)
    $map = [
        'monday'    => 0,
        'tuesday'   => 1,
        'wednesday' => 2,
        'thursday'  => 3,
        'friday'    => 4,
        'saturday'  => 5,
        'sunday'    => 6,
    ];

    $dw = $map[$dayName];
    $pgDow = ($dw + 1) % 7; // Correcto: monday(0) → 1, sunday(6) → 0

    try {
        DB::transaction(function () use ($user, $dayName, $isDayOff, $pgDow) {

            // 1. Actualizar o crear DaysOff
            $daysOff = DaysOff::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'monday'    => false,
                    'tuesday'   => false,
                    'wednesday' => false,
                    'thursday'  => false,
                    'friday'    => false,
                    'saturday'  => false,
                    'sunday'    => false,
                ]
            );

            $daysOff->{$dayName} = $isDayOff;
            $daysOff->save();

            // Log para debug
            Log::info('DaysOff actualizado', [
                'user_id' => $user->id,
                'day'     => $dayName,
                'value'   => $isDayOff,
            ]);

            // 2. Actualizar schedules (solo si existen)
            $affected = Schedule::where('user_id', $user->id)
                ->whereRaw("EXTRACT(DOW FROM date) = ?", [$pgDow])
                ->where('is_blocked', false)
                ->update([
                    'is_available' => !$isDayOff,
                    'updated_at'   => now(),
                ]);

            Log::info('Schedules actualizados', [
                'user_id'       => $user->id,
                'pg_dow'        => $pgDow,
                'new_available' => !$isDayOff,
                'affected_rows' => $affected,
            ]);

            // Opcional: si affected === 0 y quieres debug extra
            if ($affected === 0) {
                Log::warning('No se actualizaron schedules', [
                    'user_id' => $user->id,
                    'pg_dow'  => $pgDow,
                    'reason'  => 'posiblemente no hay registros o todos blocked',
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Configuración de día libre actualizada correctamente',
            'day'     => $dayName,
            'value'   => $isDayOff,
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('Error SQL en toggle days-off', [
            'message' => $e->getMessage(),
            'sql'     => $e->getSql() ?? 'N/A',
            'bindings'=> $e->getBindings() ?? [],
            'code'    => $e->getCode(),
        ]);

        return response()->json([
            'message' => 'Error al actualizar la base de datos. Revisa los logs.',
            'error'   => $e->getMessage(), // ← solo si APP_DEBUG=true
        ], 500);

    } catch (\Throwable $e) {
        Log::error('Error general en DaysOff::toggle', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json(['message' => 'Error interno del servidor'], 500);
    }
}
    
}
