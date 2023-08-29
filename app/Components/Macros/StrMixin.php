<?php

namespace App\Components\Macros;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class StrMixin
{
    public function isIp()
    {
        return function ($value) {
            return (bool) filter_var($value, FILTER_VALIDATE_IP);
        };
    }

    public function isIpv4()
    {
        return function ($value) {
            return (bool) filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        };
    }

    public function isIpv6()
    {
        return function ($value) {
            return (bool) filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        };
    }

    public function shortName()
    {
        return function ($class) {
            $parts = explode('\\', $class);

            return array_pop($parts);
        };
    }

    public function stripTags()
    {
        return function ($value, $allowed = null) {
            return strip_tags($value, $allowed);
        };
    }

    public function sentences()
    {
        return function ($value, $count = null) {
            if (preg_match_all('/((?:[A-Z]\.|[^\.!?])+)[\.!?]/', $value, $matches) > 0) {
                $sentences = $matches[0];
            } else {
                $sentences = [$value];
            }

            $sentences = Arr::map($sentences, function ($value) {
                $sentence = Str::of($value)->trim();

                return (string) $sentence->whenTest('/[^.]$/', fn ($value) => $value.'.');
            });

            return ! is_null($count) ? array_slice($sentences, 0, $count) : $sentences;
        };
    }

    public function uniqueUrl()
    {
        return function ($url, $asHtml = true) {
            $url .= (Str::contains($url, '?') ? '&' : '?').(string) Str::orderedUuid();

            return $asHtml ? new HtmlString($url) : $url;
        };
    }
}
