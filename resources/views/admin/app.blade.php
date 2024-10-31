<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
<x-code-credit />

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @vite('resources/scss/admin/all.scss')

        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div id="root"></div>


        <script>
            @isset($accessToken)
            window.accessToken = @js($accessToken);
            @endisset

            @isset($refreshToken)
            window.refreshToken = @js($refreshToken);
            @endisset
        </script>

        @viteReactRefresh
        @vite('resources/ts/admin/index.tsx')
    </body>
</html>
