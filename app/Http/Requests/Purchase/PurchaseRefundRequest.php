<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRefundRequest extends FormRequest
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
