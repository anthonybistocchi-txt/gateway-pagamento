<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'amount'            => 'required|integer|min:1',
            'client_id'         => 'required|integer|exists:clients,id',
            'product_id'        => 'required|integer|exists:products,id',
            'payment_method'    => 'required|string|in:card_credit,pix,boleto,card_debit',
            'card_last_numbers' => 'required_if:payment_method,card_credit,card_debit|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'             => 'amount is required.',
            'amount.integer'              => 'amount must be an integer.',
            'amount.min'                  => 'amount must be at least 1.',
            'client_id.required'            => 'client ID is required.',
            'client_id.integer'             => 'client ID must be an integer.',
            'client_id.exists'              => 'client ID does not exist.',
            'product_id.required'           => 'product ID is required.',
            'product_id.integer'            => 'product ID must be an integer.',
            'product_id.exists'             => 'product ID does not exist.',
            'payment_method.required'       => 'payment method is required.',
            'payment_method.string'         => 'payment method must be a string.',
            'payment_method.in'             => 'payment method must be one of: card_credit, pix, boleto, card_debit.',
            'card_last_numbers.required_if' => 'card last numbers are required when payment method is card_credit or card_debit.',
            'card_last_numbers.string'      => 'card last numbers must be a string.',
            'card_last_numbers.size'        => 'card last numbers must be exactly 4 characters.',
        ];
    }
}
