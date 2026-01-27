<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    public function checkNew(): JsonResponse
    {
        $notifications = Notification::where('read', false)->get();

        if ($notifications->isNotEmpty()) {
            // Marcar como leídas
            Notification::where('read', false)->update(['read' => true]);

            return response()->json([
                'hasNew' => true,
                'notifications' => $notifications
            ]);
        }

        return response()->json(['hasNew' => false]);
    }
}
