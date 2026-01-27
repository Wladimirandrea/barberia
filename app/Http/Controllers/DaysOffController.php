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
            'day'   => 'required|string',
            'value' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        /**
         * Mapa basado en WEEKDAY() de MySQL:
         * Lunes = 0, Martes = 1, Miércoles = 2, Jueves = 3, 
         * Viernes = 4, Sábado = 5, Domingo = 6.
         */
        $map = [
            'monday'    => 0,
            'tuesday'   => 1,
            'wednesday' => 2,
            'thursday'  => 3,
            'friday'    => 4,
            'saturday'  => 5,
            'sunday'    => 6,
        ];

        $dayName = strtolower($payload['day']);
        
        if (!array_key_exists($dayName, $map)) {
            return response()->json(['message' => 'Día inválido'], 422);
        }

        $dw = $map[$dayName];
        $isDayOff = (bool) $payload['value'];
        $user = $request->user();

        try {
            DB::transaction(function () use ($user, $dayName, $isDayOff, $dw) {
                // 1. Actualizar o crear la preferencia en days_offs
                $daysOff = DaysOff::firstOrCreate(['user_id' => $user->id]);
                $daysOff->{$dayName} = $isDayOff;
                $daysOff->save();

                // 2. Sincronizar con la tabla schedules
                // Si isDayOff es true, is_available debe ser false.
                Schedule::where('user_id', $user->id)
                    ->whereRaw("WEEKDAY(`date`) = ?", [$dw])
                    ->where('is_blocked', false)
                    ->update([
                        'is_available' => !$isDayOff, 
                        'updated_at'   => now()
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Configuración de día libre actualizada',
                'day'     => $dayName,
                'value'   => $isDayOff
            ]);

        } catch (\Throwable $e) {
            Log::error('DaysOff::toggle error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}