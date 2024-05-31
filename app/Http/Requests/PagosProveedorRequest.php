<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagosProveedorRequest extends FormRequest
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
			'pedido_id' => 'required',
			'monto' => 'required',
			'fecha_pago' => 'required',
			'referencia_pago' => 'required|string',
			'numero_factura' => 'required|string',
			'estado' => 'required|string',
        ];
    }
}
