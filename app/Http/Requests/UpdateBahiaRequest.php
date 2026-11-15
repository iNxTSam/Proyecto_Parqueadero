<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBahiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'numero' => [
                'required',
                'string',
                'max:10',
                Rule::unique('bahias', 'numero')->ignore($this->route('bahia')),
            ],
            'tipo_vehiculo_id' => ['required', 'integer', 'exists:tipos_vehiculo,id'],
            'capacidad_maxima' => ['required', 'integer', 'min:1', 'max:500'],
            'ubicacion' => ['nullable', 'string', 'max:100'],
            'activa' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tipo_vehiculo_id' => 'tipo de vehículo',
            'capacidad_maxima' => 'capacidad',
        ];
    }
}
