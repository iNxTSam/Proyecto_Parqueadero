<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'cedula' => [
                'required',
                'string',
                'max:20',
                Rule::unique('usuarios', 'cedula')->ignore($this->route('usuario')),
            ],
            'carnet' => ['nullable', 'string', 'max:20'],
            'contacto' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'rol_id' => ['required', 'integer', 'in:2,3'],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'rol_id' => 'rol',
            'password' => 'contraseña',
        ];
    }
}
