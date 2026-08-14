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
        // product_id is required when creating (POST), but optional when updating (PUT/PATCH)
        $productIdRules = $this->isMethod('post')
            ? ['required', 'exists:products,id']
            : ['sometimes', 'exists:products,id'];

        return [
            'product_id' => $productIdRules,
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
