<?php

namespace App\Http\Requests;

use App\Components\Search\ParsedQuery;
use App\Components\Search\QueryParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Stringable;

class BlogSearchRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'blog.search';

    protected ?ParsedQuery $parsed;

    public function __construct(
        public readonly QueryParser $queryParser
    ) {}

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
     * Gets the search query
     */
    public function getSearchQuery(): Stringable
    {
        return $this->str('q');
    }

    /**
     * Gets parsed search query
     */
    public function parsedSearchQuery(): ParsedQuery
    {
        if (isset($this->parsed)) {
            return $this->parsed;
        }

        return $this->parsed = $this->queryParser->parse((string) $this->getSearchQuery());
    }
}
