<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id'
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($request, $user) {

            $schedule = Schedule::where('id', $request->schedule_id)
                ->lockForUpdate()
                ->first();

            if (!$schedule || !$schedule->is_available || $schedule->is_blocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este horario ya no está disponible'
                ], 409);
            }

            $appointment = Appointment::create([
                'user_id'     => $user->id,
                'schedule_id' => $schedule->id,
                'status'      => 'reservada'
            ]);

            $schedule->update(['is_available' => false]);

            // 🟩 Notificación para ADMIN
            Notification::create([
                'user_id' => null, // notificación global para admin
                'type'    => 'appointment_created',
                'message' => 'Nueva cita creada por: ' . $user->name,
                'seen'    => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada correctamente',
                'appointment' => $appointment
            ], 201);
        });
    }


    public function userAppointments()
    {
        $user = Auth::user();

        $appointments = Appointment::with('schedule')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return $appointments->map(function ($a) {
            return [
                'id'         => $a->id,
                'status'     => $a->status,
                'date'       => $a->schedule->date,

                'start_time' => \Carbon\Carbon::parse($a->schedule->start_time)->format('g:i A'),
                'end_time'   => \Carbon\Carbon::parse($a->schedule->end_time)->format('g:i A'),
            ];
        });
    }


    public function cancel(Appointment $appointment)
    {
        return DB::transaction(function () use ($appointment) {

            $appointment->update(['status' => 'cancelada']);

            $schedule = $appointment->schedule()->lockForUpdate()->first();
            if ($schedule) {
                $schedule->update(['is_available' => true]);
            }

            // 🟩 Notificación para el usuario
            Notification::create([
                'user_id' => $appointment->user_id,
                'type'    => 'appointment_cancelled_user',
                'message' => 'Has cancelado tu cita.',
                'seen'    => false,
            ]);

            // 🟩 Notificación para el admin
            Notification::create([
                'user_id' => null,
                'type'    => 'appointment_cancelled_user_admin',
                'message' => 'El usuario ' . $appointment->user->name . ' ha cancelado su cita.',
                'seen'    => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada correctamente',
                'appointment' => $appointment->fresh()
            ]);
        });
    }



    public function adminCancel(Appointment $appointment)
    {
        if ($appointment->status === 'cancelada') {
            return response()->json([
                'success' => false,
                'message' => 'La cita ya está cancelada'
            ], 409);
        }

        $appointment->update(['status' => 'cancelada']);
        $appointment->schedule->update([ 'is_available' => 1 ]);

        // 🟩 Notificación para el usuario
        Notification::create([
            'user_id' => $appointment->user_id,
            'type'    => 'appointment_cancelled_admin',
            'message' => 'Tu cita ha sido cancelada por el administrador.',
            'seen'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita cancelada correctamente',
            'appointment' => $appointment
        ]);
    }


    public function adminIndex()
    {
        $appointments = Appointment::with(['user', 'schedule'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id'         => $a->id,
                    'user'       => $a->user->name,
                    'date'       => $a->schedule->date->format('M-d-Y'),
                    'start_time' => \Carbon\Carbon::parse($a->schedule->start_time)->format('g:i A'),
                    'end_time'   => \Carbon\Carbon::parse($a->schedule->end_time)->format('g:i A'),

                    'status'     => $a->status,
                ];
            });

        return response()->json($appointments);
    }



    public function confirm(Appointment $appointment)
    {
        if ($appointment->status === 'confirmada') {
            return response()->json([
                'success' => false,
                'message' => 'La cita ya está confirmada'
            ], 409);
        }

        $appointment->update(['status' => 'confirmada']);

        // 🟩 Notificación para el usuario
        Notification::create([
            'user_id' => $appointment->user_id,
            'type'    => 'appointment_confirmed',
            'message' => 'Tu cita ha sido confirmada.',
            'seen'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita confirmada correctamente',
            'appointment' => $appointment
        ]);
    }

    public function markAsAttended(Appointment $appointment)
    {
        if ($appointment->status === 'atendido') {
            return response()->json([
                'success' => false,
                'message' => 'La cita ya fue marcada como atendida'
            ], 409);
        }

        $appointment->update(['status' => 'atendido']);

        // Notificación para el usuario
        Notification::create([
            'user_id' => $appointment->user_id,
            'type'    => 'appointment_attended',
            'message' => 'Tu cita ha sido marcada como atendida.',
            'seen'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita marcada como atendida',
            'appointment' => $appointment
        ]);
    }
}
