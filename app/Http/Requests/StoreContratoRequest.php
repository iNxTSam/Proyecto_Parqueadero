<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'vehiculo_id' => ['required', 'string', 'max:10', 'exists:vehiculos,placa'],
            'tarifa_id' => ['required', 'integer', 'exists:tarifas,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'tarifa_id' => 'tarifa',
        ];
    }
}
