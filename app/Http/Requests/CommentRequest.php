<?php

namespace App\Http\Requests;

use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use App\Components\Settings\Facades\PageSettings;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Represents a comment post request.
 *
 * @property-read ?string $title
 * @property-read string $comment
 */
class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Use the comment policy to check if allowed.
        return Gate::allows('create', [Comment::class, $this->article]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        $rules = [
            'title' => 'sometimes|string|max:255',
            'comment' => 'required|string',
        ];

        if ($this->isGuest()) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class),
            ];
        }

        $useCaptcha = PageSettings::page('blog')->setting('use_captcha');

        if (Captcha::getDriver('recaptcha')->isReady() && (($useCaptcha === 'guest' && $this->isGuest()) || $useCaptcha === 'all')) {
            $rules['g-recaptcha-response'] = CaptchaRule::required('recaptcha');
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'A user with that e-mail address already exists. Please login and try again.',
        ];
    }

    /**
     * Checks if being posted as guest
     */
    public function isGuest(): bool
    {
        return is_null($this->user());
    }
}
