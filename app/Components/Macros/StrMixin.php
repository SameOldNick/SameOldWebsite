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

    public function appendQuery()
    {
        return function ($url, $query, $replace = false) {
            $query = is_string($query) ? $query : Arr::query($query);
            $lastQuestionMarkPos = strrpos($url, '?');

            if ($replace && $lastQuestionMarkPos !== false) {
                $url = Str::substr($url, 0, $lastQuestionMarkPos);
                $lastQuestionMarkPos = false;
            }

            return implode('', [
                $url,
                $lastQuestionMarkPos !== false ? '&' : '?',
                $query,
            ]);
        };
    }

    public function l33t()
    {
        return function ($text) {
            // Define a simple mapping of l33t characters to their English equivalents
            $mapping = [
                'a' => ['4', '@'],
                'A' => ['4'],
                'B' => ['8'],
                'c' => ['(', '[', '<'],
                'C' => ['(', '[', '<'],
                'e' => ['3'],
                'E' => ['3'],
                'g' => ['6', '9'],
                'G' => ['6', '9'],
                'i' => ['1', '!'],
                'I' => ['1', '!'],
                'l' => ['|'],
                'o' => ['0'],
                'O' => ['0'],
                's' => ['$', '5'],
                'S' => ['$', '5'],
                't' => ['7', '+'],
                'T' => ['7'],
                'z' => ['2'],
                'Z' => ['2'],
            ];

            // Replace English characters with their l33t equivalents
            return strtr($text, Arr::map($mapping, fn ($chars) => Arr::random($chars)));
        };
    }

    public function unl33t()
    {
        return function ($text) {
            // Define a simple mapping of l33t characters to their English equivalents
            $mapping = [
                '4' => 'a',
                '@' => 'a',
                '8' => 'b',
                '(' => 'c',
                '[' => 'c',
                '<' => 'c',
                '3' => 'e',
                '6' => 'g',
                '9' => 'g',
                '1' => 'i',
                '!' => 'i',
                '|' => 'l',
                '0' => 'o',
                '$' => 's',
                '5' => 's',
                '7' => 't',
                '+' => 't',
                '2' => 'z',
            ];

            // Replace l33t characters with their English equivalents
            return strtr($text, $mapping);
        };
    }

    public function secureEquals()
    {
        return function ($known, $user) {
            return hash_equals($known, $user);
        };
    }

    public function splitIntoChunks()
    {
        return function ($str, $maxLength) {
            $chunks = [];
            $length = Str::length($str);

            for ($start = 0; $start < $length; $start += $maxLength) {
                $chunks[] = Str::substr($str, $start, $maxLength);
            }

            return $chunks;
        };
    }
}
