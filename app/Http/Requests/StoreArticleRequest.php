<?php

namespace App\Http\Requests;

use App\Rules\Slugified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                new Slugified,
                Rule::unique('articles'),
            ],
            'revision' => 'required|array:content,summary',
            'revision.content' => 'required|string',
            'revision.summary' => 'nullable|string',
            'main_image' => 'sometimes|array',
            'main_image.image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_image.description' => 'string|max:255',
            'images' => 'sometimes|array',
            'images.*' => 'uuid',
            'tags' => 'sometimes|array',
            'tags.*' => 'string',
            'published_at' => 'nullable|date',
        ];
    }
}
