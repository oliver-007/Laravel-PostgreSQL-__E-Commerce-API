<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Require product_id on POST, but validate it only if present on PUT/PATCH
        $productIdRule = $this->isMethod('post') ? ['required'] : ['sometimes'];

        return [
            'product_id' => array_merge($productIdRule, ['exists:products,id']),
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
