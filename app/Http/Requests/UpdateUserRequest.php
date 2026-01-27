<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permite que cualquier usuario autenticado use este request
    }

    public function rules(): array
    {
        return [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('user')->id),
            ],
            'password' => 'sometimes|required|min:6',
            'role'     => 'sometimes|required|string|in:admin,user',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'imagen'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
