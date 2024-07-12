<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>{{ (! empty($title) ? $title . " - " : "") . config("app.name", "Laravel") }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Leckerli+One&display=swap" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"/>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"/>
    <link rel="manifest" href="/site.webmanifest"/>
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#047857"/>
    <meta name="msapplication-TileColor" content="#da532c"/>
    <meta name="theme-color" content="#ffffff"/>

    @vite(["resources/kocina/scss/app.scss", "resources/kocina/js/bootstrap.js"])

    {!! JsonLd::generate() !!}
</head>
<body>
<div class="navbar container">
    <div class="navbar-left">
        <a href="{{ route('home') }}" class="navbar-title">{{ env('APP_NAME') }}</a>
    </div>
    <div class="navbar-right">
        <button class="navbar-search-button">
            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle>
                <path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path>
            </svg>
        </button>
        <button class="navbar-nav-button">
            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <rect y="9" width="20" height="2"></rect>
                <rect y="3" width="20" height="2"></rect>
                <rect y="15" width="20" height="2"></rect>
            </svg>
        </button>
    </div>
</div>

{{ $slot }}

@stack("scripts")
</body>
</html>
