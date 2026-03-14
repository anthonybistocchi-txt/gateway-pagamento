<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'name'   => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'price'  => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'the field name is required.',
            'name.string'     => 'the field name must be a string.',
            'name.max'        => 'the field name must not exceed 255 characters.',
            'amount.required' => 'the field amount is required.',
            'amount.integer'  => 'the field amount must be an integer.',
            'amount.min'      => 'the field amount must be at least 0.',
            'price.required'  => 'the field price is required.',
            'price.numeric'   => 'the field price must be a number.',
            'price.min'       => 'the field price must be at least 0.',
        ];
    }
}
