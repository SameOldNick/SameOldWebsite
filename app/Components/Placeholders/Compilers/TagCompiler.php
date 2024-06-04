<?php

namespace App\Components\Placeholders\Compilers;

use App\Components\Placeholders\PlaceholderCollection;

use function Safe\preg_match_all;

class TagCompiler
{
    protected $collection;

    /**
     * Initializes Tag Compiler
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
     * @return string
     */
    public function compile(string $content)
    {
        if (! preg_match_all('/\[{1,2}([^\]]+)\]{1,2}/m', $content, $matches)) {
            return $content;
        }

        $compiled = $content;

        foreach ($matches[0] as $tag) {
            if ($this->isEscaped($tag)) {
                $compiled = $this->escape($tag, $compiled);
            } else {
                $placeholder = $this->extractPlaceholder($tag);

                if ($this->collection->has($placeholder)) {
                    $compiled = $this->fill($placeholder, $compiled);
                }
            }
        }

        return $compiled;
    }

    /**
     * Checks if tag is escaped.
     *
     * @return bool
     */
    protected function isEscaped(string $tag)
    {
        return str_starts_with($tag, '[[') && str_ends_with($tag, ']]');
    }

    /**
     * Escapes tag
     *
     * @return string
     */
    protected function escape(string $tag, string $content)
    {
        $value = substr($tag, 1, -1);

        return strtr($content, [$tag => $value]);
    }

    /**
     * Extracts placeholder from tag.
     *
     * @return string
     */
    protected function extractPlaceholder(string $tag)
    {
        return trim($tag, '[]');
    }

    /**
     * Fills in tag
     *
     * @return string
     */
    protected function fill(string $placeholder, string $content)
    {
        $tag = sprintf('[%s]', $placeholder);

        $value = $this->collection->value($placeholder);

        return strtr($content, [$tag => $value]);
    }
}
