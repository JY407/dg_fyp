<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — Lcare Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: #0f172a;
        }

        /* Top Header Bar */
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 60px;
            background: #1e293b; /* slate-800 dark header */
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.3);
        }

        .admin-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .admin-topbar-breadcrumb {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .admin-topbar-breadcrumb strong {
            color: #f1f5f9;
            font-weight: 600;
        }

        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-topbar-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 9999px;
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .admin-topbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.4);
        }

        /* Main content wrapper padded area */
        .page-wrapper {
            padding: 1.75rem 2rem 3rem;
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="app-container">
        {{-- Admin Sidebar --}}
        @include('partials.admin-sidebar')

        {{-- Page Content --}}
        <main class="main-content">
            {{-- Top Header Bar --}}
            <div class="admin-topbar">
                <div class="admin-topbar-left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="admin-topbar-breadcrumb">
                        <strong>{{ __('app.app_name_admin') }}</strong>
                    </span>
                </div>
                <div class="admin-topbar-right">
                    <div class="admin-topbar-badge">
                        <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                        {{ __('app.status_system_online') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="admin-topbar-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Page Slot --}}
            <div class="page-wrapper">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>

</html>