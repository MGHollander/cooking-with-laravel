<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ (! empty($title) ? $title . " - " : "") . config("app.name", "Laravel") }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
    <link rel="manifest" href="/site.webmanifest" />
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#047857" />
    <meta name="msapplication-TileColor" content="#da532c" />
    <meta name="theme-color" content="#ffffff" />
    @isset($meta)
      {{ $meta }}
    @endif

    @vite(["resources/scss/app.scss"])
  </head>
  <body class="min-h-screen min-w-80 min-w-96 bg-gray-100 pb-8 font-sans antialiased">
    <div class="mb-8 h-5 bg-pink-300"></div>
    <div class="container mx-auto px-6">
      {{ $slot }}
    </div>
  </body>
</html>
