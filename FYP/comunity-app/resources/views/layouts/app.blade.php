<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Community Connect') — Lcare</title>
    <meta name="description" content="Lcare Community — Announcements, events, services, and more for your neighborhood.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Multicultural global styles ── */
        .mc-page-accent {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 24px;
            background: rgba(11, 17, 32, 0.7);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
        }
        .mc-page-accent-dot {
            width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
        }

        /* ── Top bar ── */
        .app-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            height: 52px;
            background: rgba(11, 17, 32, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(148,163,184,0.07);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
            box-shadow: 0 1px 12px rgba(0,0,0,0.25);
        }

        /* 3-colour accent line at top of bar */
        .app-topbar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg,
                #059669 0%, #059669 33%,
                #dc2626 33%, #dc2626 66%,
                #7c3aed 66%, #7c3aed 100%);
            opacity: 0.5;
        }

        .app-topbar-left {
            display: flex; align-items: center; gap: 10px;
        }
        .app-topbar-app-name {
            font-size: 13px; font-weight: 700;
            color: #e2e8f0; letter-spacing: 0.01em;
        }
        .app-topbar-status {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 10.5px; font-weight: 600; color: #34d399;
            background: rgba(16,185,129,.1);
            border: 1px solid rgba(16,185,129,.2);
            padding: 2px 10px; border-radius: 9999px;
        }
        .app-topbar-status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #34d399; display: inline-block;
            animation: topbar-pulse 2.5s ease-in-out infinite;
        }
        @keyframes topbar-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(0.85); }
        }

        .app-topbar-right {
            display: flex; align-items: center; gap: 12px;
        }
        .app-topbar-user {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none;
            padding: 4px 10px 4px 4px;
            border-radius: 10px;
            border: 1px solid transparent;
            transition: background 0.18s, border-color 0.18s;
        }
        .app-topbar-user:hover {
            background: rgba(99,102,241,.08);
            border-color: rgba(99,102,241,.18);
        }
        .app-topbar-avatar {
            width: 30px; height: 30px; border-radius: 9px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800; color: #fff;
            overflow: hidden; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(99,102,241,.4);
        }
        .app-topbar-username {
            font-size: 12.5px; font-weight: 600; color: #94a3b8;
        }
        .app-topbar-user:hover .app-topbar-username { color: #e2e8f0; }
    </style>

    @stack('styles')
    @livewireStyles
</head>

<body>
    <div class="app-container">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Page Content --}}
        <main class="main-content" style="display:flex; flex-direction:column;">

            {{-- Top Bar --}}
            @auth
            <div class="app-topbar">
                {{-- Left: App name + status --}}
                <div class="app-topbar-left">
                    <span class="app-topbar-app-name">{{ __('app.app_name') }}</span>
                    <span class="app-topbar-status">
                        <span class="app-topbar-status-dot"></span>
                        {{ __('app.status_online') }}
                    </span>
                </div>

                {{-- Right: Notification bell + user --}}
                <div class="app-topbar-right">
                    <livewire:notification-bell />
                    <a href="{{ route('profile.edit') }}" class="app-topbar-user">
                        <div class="app-topbar-avatar">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <span class="app-topbar-username">{{ auth()->user()->name }}</span>
                    </a>
                </div>
            </div>
            @endauth

            {{-- Global Multicultural Hero Banner --}}
            @include('partials.multicultural-banner')

            {{-- Page Slot --}}
            <div style="flex:1;">
                {{ $slot ?? '' }}
                @yield('content')
            </div>

            {{-- Footer --}}
            @unless(request()->routeIs('chat'))
                @include('partials.footer')
            @endunless
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
