<?php

namespace App\Http\Requests\Gateway;

use Illuminate\Foundation\Http\FormRequest;

class GatewayActivateAndDeactivateRequest extends FormRequest
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
            'id'        => 'required|integer|exists:gateways,id',
            'is_active' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'        => 'the gateway ID is required.',
            'id.integer'         => 'the gateway ID must be an integer.',
            'id.exists'          => 'the specified gateway does not exist.',
            'is_active.required' => 'the active status is required.',
            'is_active.boolean'  => 'the active status must be a boolean value.'
        ];
    }
}
