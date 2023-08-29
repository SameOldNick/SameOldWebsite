<?php

namespace App\Http\Requests;

use App\Http\Requests\Parsers\SearchQueryParser;
use Illuminate\Foundation\Http\FormRequest;

class BlogSearchRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'blog.search';

    protected $searchQuery;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'q' => 'string',
            'sort' => 'sometimes|string',
            'order' => 'sometimes|string',
        ];
    }

    /**
     * Gets parsed search query
     *
     * @return SearchQueryParser
     */
    public function parsedSearchedQuery()
    {
        if (isset($this->searchQuery)) {
            return $this->searchQuery;
        }

        return $this->searchQuery = (new SearchQueryParser($this))->parse();
    }
}
