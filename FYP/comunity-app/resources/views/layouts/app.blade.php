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

    <style>
        /* Global Multicultural Design System */
        .mc-strip {
            display: flex;
            height: 4px;
            width: 100%;
            flex-shrink: 0;
        }
        .mc-strip-malay   { flex: 1; background: linear-gradient(90deg, #059669, #34d399); }
        .mc-strip-chinese { flex: 1; background: linear-gradient(90deg, #dc2626, #f59e0b); }
        .mc-strip-indian  { flex: 1; background: linear-gradient(90deg, #7c3aed, #f97316); }

        .mc-pill {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 700; letter-spacing: .04em;
            padding: 2px 9px; border-radius: 99px; white-space: nowrap;
        }
        .mc-pill-malay   { background: rgba(5,150,105,.15);  color: #6ee7b7; border: 1px solid rgba(5,150,105,.3); }
        .mc-pill-chinese { background: rgba(220,38,38,.15);  color: #fca5a5; border: 1px solid rgba(220,38,38,.3); }
        .mc-pill-indian  { background: rgba(124,58,237,.15); color: #c4b5fd; border: 1px solid rgba(124,58,237,.3); }
        .mc-pill-general { background: rgba(99,102,241,.15); color: #a5b4fc; border: 1px solid rgba(99,102,241,.3); }

        /* Page-level multicultural header accent */
        .mc-page-accent {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 24px;
            background: rgba(15,23,42,0.6);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
        }
        .mc-page-accent-dot {
            width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
        }
    </style>

    @stack('styles')
    @livewireStyles
</head>

<body>
    <div class="app-container">
        {{-- Include Sidebar --}}
        @include('partials.sidebar')

        {{-- Page Content --}}
        <main class="main-content" style="display:flex; flex-direction:column;">

            {{-- Top Bar --}}
            @auth
            <div style="
                position: sticky;
                top: 0;
                z-index: 40;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 24px;
                height: 52px;
                background: #1e293b;
                border-bottom: 1px solid rgba(71,85,105,.35);
                flex-shrink: 0;
            ">
                {{-- Left: App label --}}
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:13px; font-weight:700; color:#e2e8f0; letter-spacing:.01em;">{{ __('app.app_name') }}</span>
                    <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:#34d399; background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); padding:2px 10px; border-radius:9999px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;"></span>
                        {{ __('app.status_online') }}
                    </span>
                </div>

                {{-- Right: Bell + User --}}
                <div style="display:flex; align-items:center; gap:12px;">
                    <livewire:notification-bell />
                    <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:8px; text-decoration:none;">
                        <div style="
                            width:32px; height:32px; border-radius:10px;
                            background:linear-gradient(135deg,#6366f1,#8b5cf6);
                            display:flex; align-items:center; justify-content:center;
                            font-size:13px; font-weight:800; color:#fff;
                            overflow:hidden;
                        ">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <span style="font-size:13px; font-weight:600; color:#cbd5e1;">{{ auth()->user()->name }}</span>
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
</body>

</html>
