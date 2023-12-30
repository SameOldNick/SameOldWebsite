<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .error-container {
            text-align: center;
        }

        .error-code {
            font-size: 10rem;
            font-weight: bold;
            color: #dc3545;
        }

        .error-message {
            font-size: 2rem;
            color: #343a40;
        }

        .nav-links {
            margin-top: 20px;
        }
    </style>

    @stack('head')
</head>

<body>
    <main class="error-container">
        <div class="error-code">@yield('code')</div>
        <div class="error-message">@yield('message', __('An unknown error occurred.'))</div>
        <ul class="nav nav-links justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="javascript: window.location.reload()">Try Again</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">Back to Home</a>
            </li>
        </ul>
    </main>

    @stack('scripts')
</body>

</html>
