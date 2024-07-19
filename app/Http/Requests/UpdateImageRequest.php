<?php

namespace App\Http\Requests;

use App\Models\Revision;
use App\Models\User;
use App\Rules\Slugified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImageRequest extends FormRequest
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
            'description' => 'nullable|string|max:255',
        ];

        if ($this->canChangeUser()) {
            $rules['user'] = [
                'nullable',
                Rule::exists(User::class, 'id')
            ];
        }

        return $rules;
    }

    /**
     * Checks if user can be changed.
     *
     * @return boolean
     */
    public function canChangeUser(): bool {
        return $this->user() && $this->user()->roles->containsAll(['manage_images']);
    }
}
