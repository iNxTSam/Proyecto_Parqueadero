<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'tipo_reporte' => ['required', 'in:ocupacion,ingresos,contratos,usuarios'],
            'formato' => ['required', 'in:csv,pdf'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tipo_reporte' => 'tipo de reporte',
            'formato' => 'formato',
            'fecha_inicio' => 'fecha inicial',
            'fecha_fin' => 'fecha final',
        ];
    }
}
