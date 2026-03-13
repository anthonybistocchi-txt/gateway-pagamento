<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserByIdRequest extends FormRequest
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
            'id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'the field ID is required.',
            'id.integer'  => 'the field ID must be an integer.',
            'id.exists'   => 'the user with the provided ID does not exist.',
        ];
    }
}
