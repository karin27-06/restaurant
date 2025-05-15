<?php

namespace App\Http\Requests\almacen;
use Illuminate\Foundation\Http\FormRequest;
class StoreAlmacenRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name' => 'required|string|max:100',
            'state' => 'required|boolean',
        ];
    }
    public function messages(){
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no debe exceder los 100 caracteres.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso',

        ];
    }
}
