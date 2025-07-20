<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'quantity.required' => 'A quantidade é obrigatória',
            'quantity.integer' => 'A quantidade deve ser um número inteiro',
            'quantity.min' => 'A quantidade mínima é 1',
            'quantity.max' => 'A quantidade máxima permitida é 1000'
        ];
    }
}