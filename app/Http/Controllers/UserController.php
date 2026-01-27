<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Listar usuarios
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'users'   => $users,
        ]);
    }

    // Crear usuario
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('imagen')) {
            // Guardar en storage/app/public/users y devolver 'users/archivo.ext'
            $data['imagen'] = $request->file('imagen')->store('users', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'user'    => $user->fresh(), // fresh() aplica el accessor para imagen
        ], 201);
    }


    // Mostrar usuario
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user'    => $user,
        ]);
    }

    // Actualizar usuario
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        // Manejo de contraseña
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
            // Obtener la ruta original almacenada (ej. 'users/archivo.jpg')
            $oldImage = $user->getRawOriginal('imagen');

            if ($oldImage && $oldImage !== 'avatar.png') {
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            // Guardar en storage/app/public/users y devolver 'users/archivo.ext'
            $data['imagen'] = $request->file('imagen')->store('users', 'public');
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'user'    => $user->fresh(), // fresh() aplica el accessor para imagen
        ], 200);
    }

    // Eliminar usuario
    public function destroy(User $user): JsonResponse
    {
        // Evitar que el admin se borre a sí mismo
        if (auth('sanctum')->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario',
            ], 403);
        }

        /**
         * Limpieza de imagen al eliminar
         */
        $imageToDelete = $user->getRawOriginal('imagen');
        if ($imageToDelete && $imageToDelete !== 'avatar.png') {
            if (Storage::disk('public')->exists($imageToDelete)) {
                Storage::disk('public')->delete($imageToDelete);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    /**
     * Lógica para notificar nuevos registros en el dashboard
     */
    public function checkNewUsers(): JsonResponse
    {
        $newUsers = User::where('status', 'new')->get();

        if ($newUsers->isNotEmpty()) {
            // Marcar como revisados
            User::where('status', 'new')->update(['status' => 'regular']);

            return response()->json([
                'hasNewUsers' => true,
                'count' => $newUsers->count()
            ]);
        }

        return response()->json(['hasNewUsers' => false]);
    }
}
