<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImageRequest extends FormRequest
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
            'image' => 'required|image',
            'description' => 'nullable|string|max:255',
        ];

        if ($this->canAssignUser()) {
            $rules['user'] = [
                'nullable',
                Rule::exists(User::class, 'id'),
            ];
        }

        return $rules;
    }

    /**
     * Checks if user can be assigned.
     */
    public function canAssignUser(): bool
    {
        return $this->user() && $this->user()->roles->containsAll(['manage_images']);
    }
}
