<?php

return [
    'github' => [
        'client_id' => env('OAUTH_GITHUB_CLIENT_ID', ''),
        'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET', ''),
    ],
    'google' => [
        'client_id' => env('OAUTH_GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET', ''),
    ],
    'twitter' => [
        'client_id' => env('OAUTH_TWITTER_CLIENT_ID', ''),
        'client_secret' => env('OAUTH_TWITTER_CLIENT_SECRET', ''),
    ],
];
