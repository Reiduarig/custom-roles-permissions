<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Rules\CanAssignRole;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //comprobar si el usuario tiene permiso para actualizar el usuario objetivo
        return $this->user()->can('update', $this->route('user')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($this->route('user')),
                'password' => ['sometimes', 'string', Password::defaults()],
            ],
            'password' => ['sometimes', 'string', Password::defaults()],
            'roles' => ['sometimes', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,slug', new CanAssignRole()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'roles.array' => 'Roles must be an array.',
            'roles.min' => 'At least one role must be assigned to the user.',
            'roles.*.exists' => 'The selected role is invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'roles.*' => 'role',
        ];
    }
}