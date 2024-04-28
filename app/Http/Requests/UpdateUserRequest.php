<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! is_null($this->user()) && $this->user()->hasAllRoles(['manage_users']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::default(),
            ],
            'state_code' => [
                'nullable',
                Rule::requiredIf(! empty($this->country_code) && ! empty($this->state_code)),
                Rule::exists('states', 'code')->where('country_code', $this->country_code),
            ],
            'country_code' => [
                'nullable',
                Rule::exists('countries', 'code'),
            ],
            'roles' => [
                'sometimes',
                'array',
            ],
            'roles.*' => [
                Rule::exists('roles', 'role'),
            ],
        ];
    }
}
