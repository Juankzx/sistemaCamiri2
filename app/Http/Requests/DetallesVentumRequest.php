<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetallesVentumRequest extends FormRequest
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
			'venta_id' => 'required',
			'producto_id' => 'required',
			'iva_id' => 'required',
			'cantidad' => 'required',
			'precio_unitario' => 'required',
        ];
    }
}
