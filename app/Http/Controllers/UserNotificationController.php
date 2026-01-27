<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class UserNotificationController extends Controller
{
    public function checkNew(Request $request)
    {
        $user = $request->user();

        // Obtener notificaciones no vistas
        $notifications = Notification::where('user_id', $user->id)
            ->where('seen', false)
            ->get()
            ->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'message' => $n->message,
                    'type'    => $n->type,
                ];
            });

        // Marcar como vistas
        Notification::whereIn('id', $notifications->pluck('id'))
            ->update(['seen' => true]);

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
