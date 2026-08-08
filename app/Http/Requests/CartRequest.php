<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
     */
    public function rules(): array
    {

        // Requir product_id on POST ,  but optional on PUT/PATCH
        $productIdRule = $this->isMethod('post') ? ['required'] : ['nullable'];

        return [
            'product_id' => array_merge($productIdRule, ['exists:products,id']),
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
