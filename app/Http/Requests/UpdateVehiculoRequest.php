<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('placa')) {
            $this->merge(['placa' => strtoupper(trim((string) $this->placa))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'placa' => [
                'required',
                'string',
                'max:10',
                Rule::unique('vehiculos', 'placa')->ignore($this->route('vehiculo')),
            ],
            'tipo_vehiculo_id' => ['required', 'integer', 'exists:tipos_vehiculo,id'],
            'propietario_id' => ['required', 'string', 'max:15', 'exists:clientes,id'],
            'color' => ['nullable', 'string', 'max:30'],
            'marca' => ['nullable', 'string', 'max:50'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tipo_vehiculo_id' => 'tipo de vehículo',
            'propietario_id' => 'propietario',
        ];
    }
}
