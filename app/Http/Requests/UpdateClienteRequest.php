<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:15',
                Rule::unique('clientes', 'id')->ignore($this->route('cliente')),
            ],
            'nombre' => ['required', 'string', 'max:70'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:50'],
            'rol_id' => ['required', 'integer', 'exists:rolclientes,id'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'id' => 'número de documento',
            'rol_id' => 'tipo de cliente',
        ];
    }
}
