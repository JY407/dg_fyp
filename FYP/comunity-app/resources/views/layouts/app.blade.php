<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Community Connect')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('resources/css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('resources/css/components/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('resources/css/components/footer.css') }}">
<link rel="stylesheet" href="{{ asset('resources/css/components/cards.css') }}">
<script src="{{ asset('resources/js/main.js') }}"></script>

    @livewireStyles
</head>
<body>
    {{-- Include Navbar --}}
    @include('partials.navbar')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Include Footer --}}
    @include('partials.footer')

    <!-- Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
    @livewireScripts
</body>
</html>
