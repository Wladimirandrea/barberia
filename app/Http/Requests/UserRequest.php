<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia si necesitas autorización específica (ej. solo admins)
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null; // Para update, excluir el email del usuario actual

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'nullable|string|min:8', // Requerido en creación, opcional en update
            'role' => 'nullable|string|in:user,admin', // Ajusta los roles según tu app
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Imagen opcional, máximo 2MB
            'status' => 'nullable|string|in:new,active,inactive', // Ajusta los status según tu app
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'El email ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe superar los 2MB.',
        ];
    }
}