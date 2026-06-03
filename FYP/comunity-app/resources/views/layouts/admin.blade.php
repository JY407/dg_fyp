<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — Lcare Admin</title>
    <meta name="description" content="Lcare Community Admin Panel">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: #0f172a;
        }

        /* ── Admin Top Header Bar ── */
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 56px;
            background: rgba(11, 17, 32, 0.97);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(148,163,184,0.07);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.3);
            flex-shrink: 0;
        }

        /* Indigo accent line at very top */
        .admin-topbar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4);
            opacity: 0.8;
        }

        .admin-topbar-left {
            display: flex; align-items: center; gap: 10px;
        }
        .admin-topbar-left svg {
            width: 16px; height: 16px; color: #6366f1; flex-shrink: 0;
        }
        .admin-topbar-breadcrumb {
            font-size: 0.82rem; color: #64748b;
        }
        .admin-topbar-breadcrumb strong {
            color: #e2e8f0; font-weight: 700;
        }

        .admin-topbar-right {
            display: flex; align-items: center; gap: 12px;
        }
        .admin-topbar-status {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 9999px;
            background: rgba(16,185,129,.08);
            border: 1px solid rgba(16,185,129,.18);
            font-size: 0.76rem; color: #34d399; font-weight: 600;
        }
        .admin-topbar-status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #34d399; display: inline-block;
            animation: admin-status-pulse 2.5s ease-in-out infinite;
        }
        @keyframes admin-status-pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(52,211,153,0.4); }
            50%       { opacity: 0.7; box-shadow: 0 0 0 4px rgba(52,211,153,0); }
        }

        .admin-topbar-avatar {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 0.8rem;
            box-shadow: 0 2px 8px rgba(99,102,241,.45);
            overflow: hidden; flex-shrink: 0;
        }
        .admin-topbar-user-name {
            font-size: 0.82rem; font-weight: 600; color: #94a3b8;
        }

        /* ── Main content padded wrapper ── */
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
        <main class="main-content" style="display:flex; flex-direction:column; min-height:100vh;">

            {{-- Top Header Bar --}}
            <div class="admin-topbar">
                <div class="admin-topbar-left">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="admin-topbar-breadcrumb">
                        <strong>{{ __('app.app_name_admin') }}</strong>
                    </span>
                </div>

                <div class="admin-topbar-right">
                    <div class="admin-topbar-status">
                        <span class="admin-topbar-status-dot"></span>
                        {{ __('app.status_system_online') }}
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <div class="admin-topbar-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="admin-topbar-user-name">
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