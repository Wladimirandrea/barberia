<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // El usuario autenticado puede cambiar su propia contraseña
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'new_password.required'     => 'Debes ingresar una nueva contraseña.',
            'new_password.confirmed'    => 'La confirmación de la nueva contraseña no coincide.',
        ];
    }
}
