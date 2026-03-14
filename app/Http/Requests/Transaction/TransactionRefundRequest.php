<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRefundRequest extends FormRequest
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
            'id' => 'required|integer|exists:transactions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Transaction ID is required.',
            'id.integer'  => 'Transaction ID must be an integer.',
            'id.exists'   => 'Transaction ID does not exist.',
        ];
    }
}
