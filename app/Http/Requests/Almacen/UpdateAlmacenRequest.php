<?php

namespace App\Http\Requests\Almacen;
use Illuminate\Foundation\Http\FormRequest;
class UpdateAlmacenRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name' => 'required|string|min:2|max:100',
            'state' => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',

            'state.required' => 'El estado es obligatorio',
            'state.boolean' => 'El estado debe ser verdadero o falso',
        ];
    }
}
