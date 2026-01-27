<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => $user->role,
            'user'  => $user
        ], 200);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'usuario',
            'status'   => 'new',
        ]);

        // 🔔 NOTIFICACIÓN GLOBAL
        Notification::create([
            'type'    => 'user_registered',
            'message' => 'Nuevo usuario registrado: ' . $user->name,
            'read'    => false,
        ]);

        // 🔐 Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => $user->role,
            'user'  => $user
        ], 201);
    }

    // Logout
    public function logout(LogoutRequest $request)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
            return response()->json(['message' => 'Logout exitoso']);
        }

        return response()->json(['message' => 'No hay sesión activa'], 400);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual no es correcta.'
            ], 422);
        }

        // Actualizar nueva contraseña
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente.'
        ], 200);
    }
}
