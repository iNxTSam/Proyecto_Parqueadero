<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenovarContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'tarifa_id' => ['required', 'integer', 'exists:tarifas,id'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tarifa_id' => 'tarifa',
        ];
    }
}
