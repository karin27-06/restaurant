<?php

namespace App\Http\Requests\Cliente;
use Illuminate\Foundation\Http\FormRequest;
class StoreCustomerRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'codigo' => 'required|string|max:11|unique:customers,codigo',
            'client_type_id' => 'required|exists:client_types,id',
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',

            'codigo.required' => 'El código es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no debe exceder los 11 caracteres.',
            'codigo.unique' => 'El código ya está en uso.',

            'client_type_id.required' => 'El tipo de cliente es obligatorio.',
            'client_type_id.exists' => 'El tipo de cliente seleccionado no es válido.',
        ];
    }
}
