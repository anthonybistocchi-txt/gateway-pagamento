<?php

namespace App\Http\Requests\Gateway;

use Illuminate\Foundation\Http\FormRequest;

class GatewayUpdatePriorityRequest extends FormRequest
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
            'id'       => 'required|integer|exists:gateways,id',
            'priority' => 'required|integer|min:0|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'       => 'the gateway ID is required.',
            'id.integer'        => 'the gateway ID must be an integer.',
            'id.exists'         => 'the specified gateway does not exist.',
            'priority.required' => 'the priority is required.',
            'priority.integer'  => 'the priority must be an integer.',
            'priority.min'      => 'the priority must be at least 0.',
            'priority.max'      => 'the priority must not exceed 100.'
        ];
    }
}
