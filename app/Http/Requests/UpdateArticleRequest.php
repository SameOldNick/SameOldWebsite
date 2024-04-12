<?php

namespace App\Http\Requests;

use App\Models\Revision;
use App\Rules\Slugified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                new Slugified,
            ],
            'published_at' => 'nullable|date',
            'current_revision' => [
                'nullable',
                'uuid',
                Rule::exists(Revision::class, 'uuid')->where('article_id', $this->article->getKey()),
            ],
        ];
    }
}
