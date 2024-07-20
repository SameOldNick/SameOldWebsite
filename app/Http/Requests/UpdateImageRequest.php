<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\Support\HasRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImageRequest extends FormRequest
{
    use HasRoles;

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

        if ($this->hasRoles(['manage_images'])) {
            $rules['user'] = [
                'nullable',
                Rule::exists(User::class, 'id'),
            ];
        }

        return $rules;
    }
}
