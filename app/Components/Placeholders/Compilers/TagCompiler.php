<?php

namespace App\Components\Placeholders\Compilers;

use App\Components\Placeholders\PlaceholderCollection;
use function Safe\preg_match_all;

class TagCompiler
{
    protected $collection;

    /**
     * Initializes Tag Compiler
     *
     * @param PlaceholderCollection $collection
     */
    public function __construct(PlaceholderCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Gets placeholder collection
     *
     * @return PlaceholderCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Compiles tags
     *
     * @param string $content
     * @return string
     */
    public function compile(string $content)
    {
        if (! preg_match_all('/\[([^\]]+)\]/m', $content, $matches)) {
            return $content;
        }

        $compiled = $content;

        foreach ($matches[1] as $placeholder) {
            if ($this->collection->has($placeholder)) {
                $compiled = $this->replace($placeholder, $compiled);
            }
        }

        return $compiled;
    }

    /**
     * Fills in tag
     *
     * @param string $placeholder
     * @param string $content
     * @return string
     */
    protected function replace(string $placeholder, string $content)
    {
        $tag = sprintf('[%s]', $placeholder);

        $value = $this->collection->value($placeholder);

        return strtr($content, [$tag => $value]);
    }
}
