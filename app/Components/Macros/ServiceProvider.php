<?php

namespace App\Components\Macros;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Arr::mixin(new ArrMixin);
        Str::mixin(new StrMixin);
        Collection::mixin(new CollectionMixin);

        Arr::macro('export', function ($array, $ignoreIndexes = true, $shortSyntax = true, $indent = '    ') {
            return ServiceProvider::generateArrayCode($array, 1, $ignoreIndexes, $shortSyntax, $indent);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Generates array as PHP code
     *
     * @param array $array
     * @param int $depth Depth of array (used to generate indentation)
     * @param bool $ignoreIndexes Whether to ignore indexes in code
     * @param bool $shortSyntax If true, the short syntax '[]' is used. If false, the long syntax 'array()' is used.
     * @param string $indent What to use for indentation (spaces or tabs)
     * @return string
     */
    public static function generateArrayCode($array, $depth, $ignoreIndexes, $shortSyntax, $indent)
    {
        // If array is empty, return single line with opening/closing array tags.
        if (empty($array)) {
            return $shortSyntax ? '[]' : 'array()';
        }

        $str = ($shortSyntax ? '[' : 'array(').PHP_EOL;

        foreach ($array as $key => $value) {
            $str .= Str::repeat($indent, $depth);

            // Include index if $ignoreIndexes is false or the key is not a positive integer
            if (! ($ignoreIndexes && is_int($key) && abs($key) === $key)) {
                $str .= sprintf('%s => ', var_export($key, true));
            }

            if (Arr::accessible($value)) {
                $str .= self::generateArrayCode($value, $depth + 1, $ignoreIndexes, $shortSyntax, $indent).',';
            } else {
                $str .= sprintf('%s,', var_export($value, true));
            }

            $str .= PHP_EOL;
        }

        // Remove last comma
        $str = Str::replaceLast(',', '', $str);

        // Add closing square bracket (tabbed back one)
        $str .= Str::repeat($indent, $depth - 1).($shortSyntax ? ']' : ')');

        return $str;
    }
}
