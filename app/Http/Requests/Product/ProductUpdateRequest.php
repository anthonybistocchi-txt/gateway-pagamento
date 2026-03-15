<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Pega o parâmetro {id} da URL e mescla nos dados que serão validados
        $this->merge([
            'id' => $this->route('id'), 
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'     => 'required|integer|exists:products,id',
            'name'   => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'     => 'the field id is required.',
            'id.integer'      => 'the field id must be an integer.',
            'id.exists'       => 'the product with the provided id does not exist.',
            'name.required'   => 'the field name is required when present.',
            'name.string'     => 'the field name must be a string.',
            'name.max'        => 'the field name must not exceed 255 characters.',
            'amount.required' => 'the field amount is required when present.',
            'amount.integer'  => 'the field amount must be an integer.',
            'amount.min'      => 'the field amount must be at least 0.',
        ];
    }
}
