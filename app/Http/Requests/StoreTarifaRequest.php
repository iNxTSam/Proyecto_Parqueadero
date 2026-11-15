<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarifaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50', 'unique:tarifas,nombre'],
            'tipo' => ['required', 'string', 'in:Dia,Semana,Quincena,Mes'],
            'tipo_vehiculo_id' => ['required', 'integer', 'exists:tipos_vehiculo,id'],
            'valor' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'dias' => ['required', 'integer', 'min:1', 'max:365'],
            'activa' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tipo_vehiculo_id' => 'tipo de vehículo',
            'dias' => 'duración en días',
        ];
    }
}
