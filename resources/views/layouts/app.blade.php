<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5efe0">
    <meta name="description" content="@yield('meta_description', 'Kenali, jelajahi, dan lestarikan warisan budaya Indonesia bersama WARNA BUDAYA.')">
    <title>@yield('title', 'WARNA BUDAYA — Kenali, Jelajahi, Lestarikan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
