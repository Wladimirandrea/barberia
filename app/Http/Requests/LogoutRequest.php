<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
          return $this->user() !== null;
    }

    public function rules(): array
    {
        // No necesitamos validar campos, pero dejamos vacío
        return [];
    }

    public function messages(): array
    {
        return [
            // Mensajes personalizados si en algún momento agregas reglas
        ];
    }
}
