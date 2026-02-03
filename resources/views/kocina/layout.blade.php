<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ (! empty($title) ? $title . " - " : "") . config("app.name") }}</title>

    @if (!empty($meta))
      {{ $meta }}
    @endif

    @if (isset($open_graph) && is_array($open_graph))
      @foreach ($open_graph as $key => $value)
        @if ($value !== null)
          <meta property="og:{{ $key }}" content="{{ $value }}" />
        @endif
      @endforeach
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap" />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Leckerli+One&display=swap" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap"
      media="print"
      onload="this.media='all'"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Leckerli+One&display=swap"
      media="print"
      onload="this.media='all'"
    />
    <noscript>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap" />
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Leckerli+One&display=swap" />
    </noscript>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2.0" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2.0" />
    <link rel="manifest" href="/site.webmanifest?v=2.0" />
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=2.0" color="#eb4a36" />
    <link rel="shortcut icon" href="/favicon.ico?v=2.0" />
    <meta name="msapplication-TileColor" content="#da532c" />
    <meta name="theme-color" content="#ffffff" />

    @vite(["resources/kocina/scss/app.scss", "resources/kocina/js/bootstrap.js"])

    {!! JsonLd::generate() !!}
  </head>
  <body>
    <x-kocina.navbar />
    <x-kocina.flash-messages />

    {{ $slot }}

    <div class="container footer">
      <div class="footer-text">
        {!! __("app.copyright") !!}
      </div>
      <div class="footer-links"><a href="{{ route('privacy.' . app()->getLocale()) }}">Privacy</a></div>
    </div>

    @stack("scripts")
  </body>
</html>
