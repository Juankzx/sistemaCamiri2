<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'user_id' => 'required',
			'sucursal_id' => 'required',
			'metodo_pago_id' => 'required',
			'caja_id' => 'required',
			'fecha' => 'required',
			'total' => 'required',
        ];
    }
}
