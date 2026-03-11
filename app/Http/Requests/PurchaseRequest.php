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
            'customer_id'       => 'required|integer|exists:customers,id',
            'product_id'        => 'required|integer|exists:products,id',
            'payment_method'    => 'required|string|in:card_credit,pix,boleto,card_debit',
            'card_last_numbers' => 'required_if:payment_method,card_credit,card_debit|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required'          => 'Customer ID is required.',
            'customer_id.integer'           => 'Customer ID must be an integer.',
            'customer_id.exists'            => 'Customer ID does not exist.',
            'product_id.required'           => 'Product ID is required.',
            'product_id.integer'            => 'Product ID must be an integer.',
            'product_id.exists'             => 'Product ID does not exist.',
            'payment_method.required'       => 'Payment method is required.',
            'payment_method.string'         => 'Payment method must be a string.',
            'payment_method.in'             => 'Payment method must be one of: card_credit, pix, boleto, card_debit.',
            'card_last_numbers.required_if' => 'Card last numbers are required when payment method is card_credit or card_debit.',
            'card_last_numbers.string'      => 'Card last numbers must be a string.',
            'card_last_numbers.size'        => 'Card last numbers must be exactly 4 characters.',
        ];
    }
}
