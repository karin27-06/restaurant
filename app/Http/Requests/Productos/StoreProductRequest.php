<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100',
            'details'     => 'nullable|string|max:255',
            'idCategory' => 'required|exists:categories,id',
            'idAlmacen'  => 'required|exists:almacens,id',
            'state'       => 'required|string|in:activo,inactivo',  // Se valida como string
        ];
    }
}
