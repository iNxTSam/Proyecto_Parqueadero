<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'numeroDocumento' => ['required', 'string', 'max:20'],
            'clave' => ['required', 'string', 'max:100'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'numeroDocumento' => 'número de documento',
            'clave' => 'contraseña',
        ];
    }
}
