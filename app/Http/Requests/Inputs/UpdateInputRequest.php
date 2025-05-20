<?php

namespace App\Http\Requests\Inputs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'name' => strtolower($this->input('name')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'idAlmacen' => 'required|exists:almacens,id',
            'idSupplier' => 'required|exists:suppliers,id',
            'description' => 'nullable|string',
            'state' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',

            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser negativa.',

            'idAlmacen.required' => 'El almacén es obligatorio.',
            'idAlmacen.exists' => 'El almacén seleccionado no existe.',

            'idSupplier.required' => 'El proveedor es obligatorio.',
            'idSupplier.exists' => 'El proveedor seleccionado no existe.',

            'description.string' => 'La descripción debe ser texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
