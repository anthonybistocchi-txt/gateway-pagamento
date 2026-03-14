<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'card_number'    => 'required_if:payment_method,card_credit,card_debit|digits:16|string',
            'name'           => 'required|string',
            'email'          => 'required|email',
            'quantity'       => 'required|integer',
            'client_id'      => 'required|integer|exists:clients,id',
            'product_id'     => 'required|integer|exists:products,id',
            'payment_method' => 'required|string|in:card_credit,pix,boleto,card_debit',
            'cvv'            => 'required_if:payment_method,card_credit,card_debit|string|size:3',
        ];
    }

    public function messages(): array
    {
        return [
            'card_number.required_if' => 'Card number is required when payment method is card_credit or card_debit.',
            'card_number.digits'      => 'Card number must be exactly 16 digits.',
            'card_number.string'      => 'Card number must be a string.',
            'name.required'           => 'name is required.',
            'name.string'             => 'name must be a string.',
            'email.required'          => 'email is required.',
            'email.email'             => 'email must be a valid email address.',
            'quantity.required'       => 'quantity is required.',
            'quantity.integer'        => 'quantity must be an integer.',
            'client_id.required'      => 'client ID is required.',
            'client_id.integer'       => 'client ID must be an integer.',
            'client_id.exists'        => 'client ID does not exist.',
            'product_id.required'     => 'product ID is required.',
            'product_id.integer'      => 'product ID must be an integer.',
            'product_id.exists'       => 'product ID does not exist.',
            'payment_method.required' => 'payment method is required.',
            'payment_method.string'   => 'payment method must be a string.',
            'payment_method.in'       => 'payment method must be one of: card_credit, pix, boleto, card_debit.',
            'cvv.required_if'         => 'CVV is required when payment method is card_credit or card_debit.',
            'cvv.string'              => 'CVV must be a string.',
            'cvv.size'                => 'CVV must be exactly 4 characters.',
        ];
    }
}
