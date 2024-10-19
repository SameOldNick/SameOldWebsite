<?php

namespace App\Http\Requests;

use App\Components\Settings\Facades\PageSettings;
use App\Rules\RecaptchaVersion3;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ];

        if (PageSettings::page('contact')->setting('require_recaptcha')) {
            $rules[recaptchaFieldName()] = ['required', new RecaptchaVersion3];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            recaptchaFieldName() => [
                'required' => __('You appear to be a robot. Please ensure your web browser supports JavaScript.')
            ]
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [];
    }
}
