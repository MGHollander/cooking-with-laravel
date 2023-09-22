<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @section('title')
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
    @endsection

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#047857">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    @if(isset($open_graph) && is_array($open_graph))
        @foreach($open_graph as $key => $value)
            <meta property="og:{{ $key }}" content="{{ $value }}"/>
        @endforeach
    @endif

    @yield('head')
</head>
<body class="font-sans antialiased min-w-80">
@include('piwik')
@yield('content')
</body>
</html>
