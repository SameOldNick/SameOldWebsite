<?php

namespace App\Components\Fakers\Providers;

use Faker\Provider\Base;
use Illuminate\Support\Arr;

class SocialMedia extends Base
{
    protected static $socialMediaPlatforms = [
        'facebook' => [
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/{username}',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'url' => 'https://twitter.com/{username}',
        ],
        'x' => [
            'name' => 'X',
            'url' => 'https://x.com/{username}',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/{username}/',
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/in/{username}/',
        ],
        'github' => [
            'name' => 'GitHub',
            'url' => 'https://github.com/{username}',
        ],
        'reddit' => [
            'name' => 'Reddit',
            'url' => 'https://www.reddit.com/u/{username}/',
        ],
    ];

    public function socialMediaPlatform()
    {
        $platform = array_rand(self::$socialMediaPlatforms);

        return self::$socialMediaPlatforms[$platform]['name'];
    }

    public function socialMediaLink(...$platforms)
    {
        $platform = ! empty($platforms) ? Arr::random($platforms) : array_rand(self::$socialMediaPlatforms);

        if (! isset(self::$socialMediaPlatforms[$platform])) {
            throw new \InvalidArgumentException("'$platform' is not a valid social media platform.");
        }

        return str_replace('{username}', $this->generator->userName, self::$socialMediaPlatforms[$platform]['url']);
    }
}
