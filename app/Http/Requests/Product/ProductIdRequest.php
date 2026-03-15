<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductIdRequest extends FormRequest
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
            'id' => 'required|integer|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'the field id is required.',
            'id.integer'  => 'the field id must be an integer.',
            'id.exists'   => 'the product with the provided id does not exist.',
        ];
    }
}
