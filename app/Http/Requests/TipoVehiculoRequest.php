<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tipos_vehiculo', 'nombre')->ignore($this->route('tipoVehiculo')),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
