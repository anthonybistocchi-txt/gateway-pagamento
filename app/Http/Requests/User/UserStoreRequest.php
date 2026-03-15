<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|integer|in:1,2,3,4',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'the field name is required.',
            'name.string'       => 'the field name must be a string.',
            'name.max'          => 'the field name must not exceed 255 characters.',
            'email.required'    => 'the field email is required.',
            'email.string'      => 'the field email must be a string.',
            'email.email'       => 'the field email must be a valid email address.',
            'email.max'         => 'the field email must not exceed 255 characters.',
            'email.unique'      => 'the email has already been taken.',
            'password.required' => 'the field password is required.',
            'password.string'   => 'the field password must be a string.',
            'password.min'      => 'the field password must be at least 8 characters.',
            'role_id.required'  => 'the field role is required.',
            'role_id.integer'   => 'the field role must be an integer.',
            'role_id.in'        => 'the selected role is invalid. Valid values are 1 (Admin), 2 (Manager), 3 (Finance), and 4 (User).',
        ];
    }
}
