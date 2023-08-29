<?php

namespace App\Http\Requests;

use App\Components\Settings\ContactPageSettings;

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
    public function rules(ContactPageSettings $settings): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ];

        if ($settings->setting('require_recaptcha'))
            $rules[recaptchaFieldName()] = recaptchaRuleName();

        return $rules;
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [];
    }
}
