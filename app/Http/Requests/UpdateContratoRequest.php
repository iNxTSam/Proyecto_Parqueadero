<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'valor' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'estado_id' => ['required', 'integer', 'exists:estados_contrato,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'estado_id' => 'estado',
        ];
    }
}
