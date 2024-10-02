<?php

namespace App\Http\Requests;

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
     * Gets how results are sorted
     *
     * @return string Either 'date' or 'relevance'
     */
    public function sortBy()
    {
        return $this->has('sort') && $this->str('sort')->lower()->exactly('date') ? 'date' : 'relevance';
    }

    /**
     * Gets how results are ordered
     *
     * @return string Either 'asc' or 'desc'
     */
    public function order()
    {
        return $this->has('order') && $this->str('order')->lower()->exactly('asc') ? 'asc' : 'desc';
    }

    /**
     * Checks if sorting by
     */
    public function isSortBy(string $sortBy): bool
    {
        return $this->sortBy() === $sortBy;
    }

    /**
     * Checks if results should be in ascending order
     */
    public function isOrderAscending(): bool
    {
        return $this->order() === 'asc';
    }

    /**
     * Checks if results should be in descending order
     */
    public function isOrderDescending(): bool
    {
        return $this->order() === 'desc';
    }

    /**
     * Gets the search query
     */
    public function getSearchQuery(): Stringable
    {
        return $this->str('q');
    }
}
