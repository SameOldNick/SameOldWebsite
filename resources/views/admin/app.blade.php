<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @vite('resources/scss/admin/all.scss')
        {{-- <link type="text/css" rel="stylesheet" href="{{ Str::uniqueUrl(mix('css/admin.css')) }}"> --}}

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

        {{-- <script src="{{ Str::uniqueUrl(mix('/js/manifest.js')) }}"></script>
        <script src="{{ Str::uniqueUrl(mix('/js/admin-vendor.js')) }}"></script>
        <script src="{{ Str::uniqueUrl(mix('/js/admin.js')) }}"></script> --}}
    </body>
</html>
