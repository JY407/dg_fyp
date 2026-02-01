<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Community Connect')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <div class="app-container">
        {{-- Include Sidebar --}}
        @include('partials.sidebar')

        {{-- Page Content --}}
        <main class="main-content">
            {{ $slot ?? '' }}
            @yield('content')

            {{-- Include Footer --}}
            {{-- Include Footer --}}
            @unless(request()->routeIs('chat'))
                @include('partials.footer')
            @endunless
        </main>
    </div>

    @livewireScripts
</body>

</html>