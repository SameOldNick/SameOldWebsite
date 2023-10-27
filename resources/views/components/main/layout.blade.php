<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Same Old Nick' : 'Same Old Nick' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/scss/main/all.scss')
    {{-- <link type="text/css" rel="stylesheet" href="{{ Str::uniqueUrl(mix('css/main.css')) }}"> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <x-sweetalerts />

    @stack('head')
</head>

<body class="bg-light">
    <header class="bg-secondary sticky-top">
        <x-main.top.navbar />
    </header>

    <main {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}
    </main>

    <x-main.bottom.footer />

    <div class="loader show">
        <div class="loader-content">
            <div class="spin-skeleton light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

    </div>

    @vite('resources/ts/main/index.ts')

    @stack('scripts')
</body>

</html>
