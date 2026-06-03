<?php

use Livewire\Volt\Component;
use App\Models\CultureEvent;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with()
    {
        return [
            'upcomingEvents' => CultureEvent::where('event_date', '>=', today())
                ->orderBy('event_date', 'asc')
                ->get(),
            'pastEvents' => CultureEvent::where('event_date', '<', today())
                ->orderBy('event_date', 'desc')
                ->get(),
        ];
    }
}; ?>

<div class="min-h-screen" style="background: #0f172a;">
    @push('styles')
    <style>
        #prayer-times-container .prayer-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        #prayer-times-container .prayer-card:hover { background: #eef2ff; border-color: #c7d2fe; }
        #prayer-times-container .prayer-name { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; margin-bottom: 4px; }
        #prayer-times-container .prayer-time { font-size: 18px; font-weight: 800; color: #1e293b; }

        /* Multicultural tri-colour strip */
        .multicultural-strip { display: flex; height: 5px; border-radius: 4px; overflow: hidden; }
        .strip-malay   { flex: 1; background: linear-gradient(90deg, #10b981, #34d399); }
        .strip-chinese { flex: 1; background: linear-gradient(90deg, #ef4444, #fbbf24); }
        .strip-indian  { flex: 1; background: linear-gradient(90deg, #8b5cf6, #f97316); }

        /* Culture pill badges */
        .culture-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 10px; font-weight: 700; letter-spacing: .04em;
            padding: 3px 10px; border-radius: 99px;
        }
        .culture-pill-malay   { background: rgba(16,185,129,.18); color: #6ee7b7; border: 1px solid rgba(16,185,129,.35); }
        .culture-pill-chinese { background: rgba(239,68,68,.18);  color: #fca5a5; border: 1px solid rgba(239,68,68,.35); }
        .culture-pill-indian  { background: rgba(139,92,246,.18); color: #c4b5fd; border: 1px solid rgba(139,92,246,.35); }
        .culture-pill-general { background: rgba(99,102,241,.18); color: #a5b4fc; border: 1px solid rgba(99,102,241,.35); }

        /* Culture-themed gradient backgrounds for placeholder images */
        .bg-placeholder-malay   { background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 70%, #6d1a0e 100%); }
        .bg-placeholder-chinese { background: linear-gradient(135deg, #450a0a 0%, #7f1d1d 40%, #991b1b 70%, #78350f 100%); }
        .bg-placeholder-indian  { background: linear-gradient(135deg, #2e1065 0%, #4c1d95 40%, #5b21b6 70%, #7c2d12 100%); }
        .bg-placeholder-general { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #3730a3 70%, #1e3a5f 100%); }

        /* Decorative dot-pattern overlay for placeholders */
        .ethnic-pattern-overlay {
            position: absolute; inset: 0; opacity: 0.12;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.4) 1px, transparent 1px),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.4) 1px, transparent 1px),
                radial-gradient(circle at 50% 80%, rgba(255,255,255,0.4) 1px, transparent 1px),
                radial-gradient(circle at 70% 60%, rgba(255,255,255,0.3) 1px, transparent 1px);
            background-size: 24px 24px, 32px 32px, 20px 20px, 28px 28px;
        }

        /* Heritage banner */
        .heritage-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1a0a2e 30%, #0c1a0c 60%, #1a0c08 100%);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }
        .heritage-banner::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse at 10% 50%, rgba(16,185,129,.12) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(239,68,68,.08) 0%, transparent 50%),
                radial-gradient(ellipse at 90% 50%, rgba(139,92,246,.12) 0%, transparent 50%);
        }

        /* Interactive Calendar styling */
        .cal-day-cell {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            position: relative;
            color: #94a3b8; /* text-slate-400 */
        }
        .cal-day-cell:hover:not(.cal-day-empty) {
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: scale(1.08);
        }
        .cal-day-cell.cal-day-active {
            background-color: #6366f1 !important; /* indigo-500 */
            color: #ffffff !important;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.5);
            font-weight: 800;
        }
        .cal-day-cell.cal-day-today:not(.cal-day-active) {
            border: 2px solid #6366f1;
            color: #ffffff;
            font-weight: 800;
        }
        .cal-day-empty {
            cursor: default;
            opacity: 0.15;
        }
        
        /* Holiday/celebration specific day classes */
        .cal-day-malay {
            background-color: rgba(16, 185, 129, 0.18);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #34d399;
        }
        .cal-day-chinese {
            background-color: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #f87171;
        }
        .cal-day-indian {
            background-color: rgba(139, 92, 246, 0.18);
            border: 1px solid rgba(139, 92, 246, 0.5);
            color: #a78bfa;
        }
        .cal-day-indigenous {
            background-color: rgba(245, 158, 11, 0.18);
            border: 1px solid rgba(245, 158, 11, 0.5);
            color: #fbbf24;
        }
        .cal-day-national {
            background-color: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #60a5fa;
        }
        
        /* Indicator dots below text in calendar cells */
        .cal-indicator-dot {
            width: 4px;
            height: 4px;
            border-radius: 9999px;
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
        }
        .bg-indicator-malay { background-color: #10b981; }
        .bg-indicator-chinese { background-color: #ef4444; }
        .bg-indicator-indian { background-color: #8b5cf6; }
        .bg-indicator-indigenous { background-color: #f59e0b; }
        .bg-indicator-national { background-color: #3b82f6; }
    </style>
    @endpush

    {{-- Page Header --}}
    <div class="px-6 pt-8 pb-4">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m12 3-1.9 5.8a2 2 0 0 1-1.287 1.288L3 12l5.8 1.9a2 2 0 0 1 1.288 1.287L12 21l1.9-5.8a2 2 0 0 1 1.287-1.288L21 12l-5.8-1.9a2 2 0 0 1-1.288-1.287Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Malaysia Culture &amp; History</h1>
                <p class="text-gray-400 text-sm mt-0.5">Discover heritage, traditions, and cultural events in our community.</p>
            </div>
        </div>
        {{-- Tri-colour multicultural strip --}}
        <div class="multicultural-strip mb-4">
            <div class="strip-malay"></div>
            <div class="strip-chinese"></div>
            <div class="strip-indian"></div>
        </div>
    </div>

    <div class="px-6 pb-10">

        {{-- Malaysia Multicultural Heritage Banner --}}
        <div class="heritage-banner p-5 mb-8 relative">
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                {{-- Culture icon trio --}}
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Malay / Islamic --}}
                    <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center gap-0.5"
                        style="background: linear-gradient(135deg, #065f46, #047857); border: 1px solid rgba(52,211,153,.25);">
                        <span class="text-2xl leading-none">🕌</span>
                        <span class="text-[9px] font-bold text-emerald-300 tracking-wide">MALAY</span>
                    </div>
                    {{-- Chinese --}}
                    <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center gap-0.5"
                        style="background: linear-gradient(135deg, #7f1d1d, #b45309); border: 1px solid rgba(252,165,0,.25);">
                        <span class="text-2xl leading-none">🏮</span>
                        <span class="text-[9px] font-bold text-yellow-300 tracking-wide">CHINESE</span>
                    </div>
                    {{-- Indian --}}
                    <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center gap-0.5"
                        style="background: linear-gradient(135deg, #4c1d95, #7c2d12); border: 1px solid rgba(167,139,250,.25);">
                        <span class="text-2xl leading-none">🪔</span>
                        <span class="text-[9px] font-bold text-violet-300 tracking-wide">INDIAN</span>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-white leading-tight">Malaysia's Multicultural Heritage</h2>
                    <p class="text-sm text-gray-400 mt-1 leading-relaxed">
                        Celebrating the rich tapestry of Malay, Chinese, Indian &amp; Indigenous traditions that make our community unique.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="culture-pill culture-pill-malay">🌙 Islam &amp; Malay</span>
                        <span class="culture-pill culture-pill-chinese">🏮 Chinese &amp; Buddhist</span>
                        <span class="culture-pill culture-pill-indian">🪔 Hindu &amp; Indian</span>
                        <span class="culture-pill culture-pill-general">✨ All Cultures</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calendar Widgets Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

            {{-- Prayer Times --}}
            <div class="rounded-2xl border border-gray-700 overflow-hidden" style="background:#1e293b;">
                <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-3"
                    style="background: linear-gradient(135deg, #064e3b, #047857);">
                    <div class="w-9 h-9 rounded-xl bg-emerald-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Islamic Prayer Times</h2>
                        <p class="text-xs text-gray-400">JAKIM Method — Kuala Lumpur</p>
                    </div>
                </div>
                <div class="p-5">
                    <div id="prayer-times-container">
                        <div class="grid grid-cols-3 gap-3 animate-pulse">
                            @for($i = 0; $i < 6; $i++)
                                <div class="h-16 bg-gray-700 rounded-xl"></div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Malaysia Festive & Celebrations Calendar --}}
            <div class="rounded-2xl border border-gray-700 overflow-hidden flex flex-col h-full animate-fadeIn" style="background:#1e293b;" wire:ignore>
                <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between"
                    style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-900/60 flex items-center justify-center border border-indigo-500/20">
                            <span class="text-lg">📅</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white">Festive Calendar</h2>
                            <p class="text-xs text-gray-400">Malaysia Cultural &amp; Public Holidays</p>
                        </div>
                    </div>
                    {{-- Navigation --}}
                    <div class="flex items-center gap-1.5">
                        <button id="cal-prev-btn" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-slate-700/50 transition-colors border border-gray-700/50" title="Previous Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <span id="cal-month-year" class="text-sm font-bold text-white px-1 min-w-24 text-center">May 2026</span>
                        <button id="cal-next-btn" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-slate-700/50 transition-colors border border-gray-700/50" title="Next Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-5 flex flex-col gap-4 flex-grow justify-between">
                    {{-- Calendar Grid Container --}}
                    <div class="flex flex-col">
                        {{-- Weekday Names --}}
                        <div class="grid grid-cols-7 text-center text-slate-400 font-bold text-xs tracking-wider mb-2 opacity-80">
                            <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                        </div>
                        {{-- Days Grid --}}
                        <div id="cal-days-grid" class="grid grid-cols-7 gap-y-1.5 gap-x-1.5 text-center text-sm font-semibold">
                            {{-- Dynamically rendered via JS --}}
                        </div>
                    </div>

                    {{-- Culture Legend --}}
                    <div class="pt-3 border-t border-gray-700/60 flex flex-wrap gap-1.5 text-[10px] font-semibold text-slate-300">
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-950/40 text-emerald-300 border border-emerald-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Malay/Islamic
                        </span>
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-950/40 text-red-300 border border-red-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Chinese
                        </span>
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-950/40 text-violet-300 border border-violet-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span> Indian
                        </span>
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-950/40 text-amber-300 border border-amber-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Indigenous
                        </span>
                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-950/40 text-blue-300 border border-blue-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> National
                        </span>
                    </div>

                    {{-- Active Day Details Panel --}}
                    <div id="cal-day-detail" class="mt-1 p-3.5 rounded-xl border transition-all duration-300 flex flex-col gap-1.5 bg-slate-900/60 border-slate-700/60 min-h-[96px] justify-center">
                        {{-- Dynamically updated details --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Culture Events Grid --}}
        @php $hasAny = $upcomingEvents->isNotEmpty() || $pastEvents->isNotEmpty(); @endphp

        @if($hasAny)

            {{-- ── Upcoming ── --}}
            @if($upcomingEvents->isNotEmpty())
                <div class="mb-5 flex items-center gap-3">
                    <span class="w-1 h-5 bg-indigo-500 rounded-full inline-block"></span>
                    <h2 class="text-xl font-bold text-white">Upcoming Cultural Events</h2>
                    <span class="ml-1 text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-900/50 text-indigo-300 border border-indigo-700/40">
                        {{ $upcomingEvents->count() }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-10">
                    @foreach($upcomingEvents as $event)
                        @php
                            $daysLeft = today()->diffInDays(\Carbon\Carbon::parse($event->event_date), false);
                            $isToday  = \Carbon\Carbon::parse($event->event_date)->isToday();
                            $isSoon   = $daysLeft <= 7 && !$isToday;

                            // Detect culture from title/description keywords
                            $kw = strtolower($event->title . ' ' . $event->description);
                            if (str_contains($kw,'hari raya')||str_contains($kw,'raya')||str_contains($kw,'aidilfitri')||str_contains($kw,'aidiladha')||str_contains($kw,'maulidur')||str_contains($kw,'israk')||str_contains($kw,'malay')||str_contains($kw,'islamic')||str_contains($kw,'ramadan')||str_contains($kw,'quran')) {
                                $bgClass     = 'bg-placeholder-malay';
                                $pillClass   = 'culture-pill-malay';
                                $pillLabel   = '🌙 Malay / Islamic';
                                $icon        = '🕌';
                            } elseif (str_contains($kw,'chinese')||str_contains($kw,'cny')||str_contains($kw,'new year')||str_contains($kw,'mid autumn')||str_contains($kw,'mooncake')||str_contains($kw,'chap goh')||str_contains($kw,'wesak')||str_contains($kw,'buddha')||str_contains($kw,'lantern')||str_contains($kw,'dragon')||str_contains($kw,'qingming')) {
                                $bgClass     = 'bg-placeholder-chinese';
                                $pillClass   = 'culture-pill-chinese';
                                $pillLabel   = '🏮 Chinese / Buddhist';
                                $icon        = '🏮';
                            } elseif (str_contains($kw,'deepavali')||str_contains($kw,'diwali')||str_contains($kw,'thaipusam')||str_contains($kw,'pongal')||str_contains($kw,'hindu')||str_contains($kw,'indian')||str_contains($kw,'tamil')||str_contains($kw,'kavadi')||str_contains($kw,'kolam')) {
                                $bgClass     = 'bg-placeholder-indian';
                                $pillClass   = 'culture-pill-indian';
                                $pillLabel   = '🪔 Indian / Hindu';
                                $icon        = '🪔';
                            } else {
                                $bgClass     = 'bg-placeholder-general';
                                $pillClass   = 'culture-pill-general';
                                $pillLabel   = '✨ Cultural Event';
                                $icon        = '🎉';
                            }
                        @endphp
                        <div class="rounded-2xl border overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group relative"
                            style="background:#1e293b; border-color: {{ $isToday ? '#6366f1' : 'rgba(71,85,105,.5)' }};">

                            {{-- Image / Multicultural Placeholder --}}
                            <div class="h-48 overflow-hidden relative">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}"
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    {{-- Gradient overlay on hover --}}
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        style="background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 60%);"></div>
                                @else
                                    {{-- Culture-themed animated placeholder --}}
                                    <div class="w-full h-full {{ $bgClass }} flex items-center justify-center relative">
                                        <div class="ethnic-pattern-overlay"></div>
                                        {{-- Decorative rings --}}
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-32 h-32 rounded-full border border-white/10 absolute"></div>
                                            <div class="w-20 h-20 rounded-full border border-white/10 absolute"></div>
                                        </div>
                                        {{-- Culture icon --}}
                                        <div class="relative z-10 flex flex-col items-center gap-2">
                                            <span class="text-5xl drop-shadow-lg">{{ $icon }}</span>
                                            <span class="text-xs font-semibold text-white/60 tracking-widest uppercase">Cultural Event</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Status badge --}}
                                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                    @if($isToday)
                                        <span class="flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-indigo-600 text-white shadow-lg">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Today
                                        </span>
                                    @elseif($isSoon)
                                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-500/90 text-white shadow-lg">
                                            {{ $daysLeft }}d away
                                        </span>
                                    @endif
                                </div>
                                {{-- Date badge --}}
                                <div class="absolute top-3 right-3 bg-slate-700/90 backdrop-blur-sm text-gray-200 text-xs font-bold px-3 py-1.5 rounded-full border border-gray-600 shadow-sm">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex flex-col justify-between min-h-[170px]">
                                <div>
                                    {{-- Culture pill --}}
                                    <div class="mb-2">
                                        <span class="culture-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors line-clamp-1">{{ $event->title }}</h3>
                                    <p class="text-sm text-gray-400 leading-relaxed line-clamp-2">{{ $event->description }}</p>
                                </div>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-700/40 pt-3">
                                    <span class="text-xs text-indigo-400 font-semibold">
                                        {{ $isToday ? 'Happening today!' : 'In ' . \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                                    </span>
                                    <button 
                                        type="button"
                                        class="text-xs text-indigo-300 hover:text-white font-bold transition-colors flex items-center gap-1 group/btn cursor-pointer"
                                        onclick="openEventDetailModal(decodeURIComponent('{{ rawurlencode($event->title) }}'), decodeURIComponent('{{ rawurlencode($event->description) }}'), '{{ $event->image_path ? asset('storage/' . $event->image_path) : '' }}', '{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}', '{{ $pillLabel }}', '{{ $pillClass }}', '{{ $bgClass }}', '{{ $icon }}')"
                                    >
                                        Read More <span class="group-hover/btn:translate-x-0.5 transition-transform inline-block">→</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ── Past ── --}}
            @if($pastEvents->isNotEmpty())
                <div class="mb-5 flex items-center gap-3">
                    <span class="w-1 h-5 bg-slate-600 rounded-full inline-block"></span>
                    <h2 class="text-xl font-bold text-slate-400">Past Events</h2>
                    <span class="ml-1 text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-500 border border-slate-700">
                        {{ $pastEvents->count() }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($pastEvents as $event)
                        @php
                            $kw2 = strtolower($event->title . ' ' . $event->description);
                            if (str_contains($kw2,'hari raya')||str_contains($kw2,'raya')||str_contains($kw2,'aidilfitri')||str_contains($kw2,'malay')||str_contains($kw2,'islamic')||str_contains($kw2,'ramadan')) {
                                $bgClass2   = 'bg-placeholder-malay';
                                $pillClass2 = 'culture-pill-malay';
                                $pillLabel2 = '🌙 Malay / Islamic';
                                $icon2      = '🕌';
                            } elseif (str_contains($kw2,'chinese')||str_contains($kw2,'cny')||str_contains($kw2,'new year')||str_contains($kw2,'mid autumn')||str_contains($kw2,'wesak')||str_contains($kw2,'lantern')||str_contains($kw2,'dragon')) {
                                $bgClass2   = 'bg-placeholder-chinese';
                                $pillClass2 = 'culture-pill-chinese';
                                $pillLabel2 = '🏮 Chinese / Buddhist';
                                $icon2      = '🏮';
                            } elseif (str_contains($kw2,'deepavali')||str_contains($kw2,'diwali')||str_contains($kw2,'thaipusam')||str_contains($kw2,'hindu')||str_contains($kw2,'indian')||str_contains($kw2,'tamil')) {
                                $bgClass2   = 'bg-placeholder-indian';
                                $pillClass2 = 'culture-pill-indian';
                                $pillLabel2 = '🪔 Indian / Hindu';
                                $icon2      = '🪔';
                            } else {
                                $bgClass2   = 'bg-placeholder-general';
                                $pillClass2 = 'culture-pill-general';
                                $pillLabel2 = '✨ Cultural Event';
                                $icon2      = '🎉';
                            }
                        @endphp
                        <div class="rounded-2xl border border-slate-700/40 overflow-hidden transition-all duration-200 group opacity-70 hover:opacity-90"
                            style="background:#1a2537;">
                            {{-- Image --}}
                            <div class="h-40 overflow-hidden relative">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}"
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                @else
                                    <div class="w-full h-full {{ $bgClass2 }} flex items-center justify-center relative grayscale group-hover:grayscale-0 transition-all duration-500">
                                        <div class="ethnic-pattern-overlay"></div>
                                        <div class="relative z-10 flex flex-col items-center gap-1">
                                            <span class="text-4xl drop-shadow-lg">{{ $icon2 }}</span>
                                            <span class="text-[10px] font-semibold text-white/50 tracking-widest uppercase">Past Event</span>
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-slate-800/90 text-slate-400 text-xs font-bold px-3 py-1.5 rounded-full border border-slate-700">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                </div>
                            </div>
                            {{-- Content --}}
                            <div class="p-4 flex flex-col justify-between min-h-[140px]">
                                <div>
                                    <div class="mb-1">
                                        <span class="culture-pill {{ $pillClass2 }}" style="opacity:.65;">{{ $pillLabel2 }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-400 mb-1 group-hover:text-slate-300 transition-colors line-clamp-1">{{ $event->title }}</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $event->description }}</p>
                                </div>
                                <div class="mt-3 flex items-center justify-between border-t border-slate-800/60 pt-2">
                                    <span class="text-xs text-slate-600">{{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}</span>
                                    <button 
                                        type="button"
                                        class="text-xs text-slate-400 hover:text-white font-bold transition-colors flex items-center gap-1 group/btn cursor-pointer"
                                        onclick="openEventDetailModal(decodeURIComponent('{{ rawurlencode($event->title) }}'), decodeURIComponent('{{ rawurlencode($event->description) }}'), '{{ $event->image_path ? asset('storage/' . $event->image_path) : '' }}', '{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}', '{{ $pillLabel2 }}', '{{ $pillClass2 }}', '{{ $bgClass2 }}', '{{ $icon2 }}')"
                                    >
                                        Read More <span class="group-hover/btn:translate-x-0.5 transition-transform inline-block">→</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif
    </div>

    {{-- Dynamic Culture Event Detail Modal --}}
    <div id="culture-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 animate-fadeIn" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop with blur --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity duration-300" onclick="closeEventDetailModal()"></div>
        
        {{-- Modal Content Box --}}
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full z-10 transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]">
            {{-- Image / Header Placeholder --}}
            <div id="modal-img-container" class="h-56 relative shrink-0">
                <img id="modal-event-img" src="" alt="" class="w-full h-full object-cover hidden">
                <div id="modal-event-placeholder" class="w-full h-full flex items-center justify-center relative">
                    <div class="ethnic-pattern-overlay"></div>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-36 h-36 rounded-full border border-white/10 absolute"></div>
                        <div class="w-24 h-24 rounded-full border border-white/10 absolute"></div>
                    </div>
                    <span id="modal-event-placeholder-icon" class="text-6xl drop-shadow-lg z-10"></span>
                </div>
                {{-- Close button --}}
                <button type="button" onclick="closeEventDetailModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-slate-950/60 backdrop-blur-sm text-slate-300 hover:text-white flex items-center justify-center border border-slate-700/30 hover:scale-105 transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-6 overflow-y-auto flex-grow flex flex-col gap-3">
                <div class="flex items-center justify-between gap-3 shrink-0">
                    <span id="modal-event-pill" class="culture-pill text-xs"></span>
                    <span id="modal-event-date" class="text-xs font-bold text-indigo-400 bg-indigo-950/40 border border-indigo-900/40 px-3 py-1 rounded-full"></span>
                </div>
                <h3 id="modal-event-title" class="text-xl font-extrabold text-white leading-tight mt-1 shrink-0 select-text"></h3>
                <div class="w-full h-px bg-slate-800/80 my-1 shrink-0"></div>
                <div class="text-sm text-slate-300 leading-relaxed overflow-y-auto pr-1 select-text" style="white-space: pre-line;">
                    <p id="modal-event-desc"></p>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/40 flex justify-end shrink-0">
                <button type="button" onclick="closeEventDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-sm font-bold text-white transition-all cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @script
    <script>
        // Prayer Times
        fetch(`https://api.aladhan.com/v1/timingsByCity?city=Kuala+Lumpur&country=Malaysia&method=17`)
            .then(res => res.json())
            .then(data => {
                if (data?.data?.timings) {
                    const t = data.data.timings;
                    const prayers = [
                        { name: 'Subuh', time: t.Fajr },
                        { name: 'Syuruk', time: t.Sunrise },
                        { name: 'Zohor', time: t.Dhuhr },
                        { name: 'Asar', time: t.Asr },
                        { name: 'Maghrib', time: t.Maghrib },
                        { name: 'Isyak', time: t.Isha }
                    ];
                    let html = '<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">';
                    prayers.forEach(p => {
                        const time = p.time.split(' ')[0];
                        html += `<div class="prayer-card"><div class="prayer-name">${p.name}</div><div class="prayer-time">${time}</div></div>`;
                    });
                    html += '</div>';
                    if (data.data.date?.hijri) {
                        const h = data.data.date.hijri;
                        html += `<div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-sm"><span class="text-gray-400">Hijri Date</span><span class="font-semibold text-gray-700">${h.day} ${h.month.en} ${h.year}</span></div>`;
                    }
                    document.getElementById('prayer-times-container').innerHTML = html;
                }
            })
            .catch(() => {
                document.getElementById('prayer-times-container').innerHTML = '<p class="text-sm text-red-400 text-center py-4">Failed to load prayer times.</p>';
            });

        // Malaysia Celebrations & Festive Calendar Logic
        const FESTIVALS_DB = {
            '2026-01-01': [{
                title: "New Year's Day",
                category: 'national',
                typeLabel: 'National Holiday',
                icon: '🎆',
                description: 'Celebrating the start of the Gregorian new year across Malaysia.'
            }],
            '2026-02-01': [{
                title: "Thaipusam",
                category: 'indian',
                typeLabel: 'Hindu Festival',
                icon: '🪔',
                description: 'A major Hindu festival celebrated by the Tamil community, dedicated to Lord Murugan. Features vibrant Kavadi processions at Batu Caves.'
            }],
            '2026-02-17': [{
                title: "Chinese New Year (Day 1)",
                category: 'chinese',
                typeLabel: 'Lunar New Year',
                icon: '🏮',
                description: 'The first day of the Lunar New Year, marked by family reunions, lion dances, and red envelopes (ang pows). Year of the Horse.'
            }],
            '2026-02-18': [{
                title: "Chinese New Year (Day 2)",
                category: 'chinese',
                typeLabel: 'Lunar New Year',
                icon: '🏮',
                description: 'The second day of Chinese New Year, characterized by visiting relatives and friends to offer blessings and gifts.'
            }],
            '2026-03-07': [{
                title: "Nuzul Al-Quran",
                category: 'malay',
                typeLabel: 'Islamic Festival',
                icon: '📖',
                description: 'Commemorates the day when the first revelation of the Quran was sent down to the Prophet Muhammad.'
            }],
            '2026-03-21': [{
                title: "Hari Raya Aidilfitri (Day 1)",
                category: 'malay',
                typeLabel: 'Eid al-Fitr',
                icon: '🌙',
                description: 'Also known as Hari Raya Puasa, celebrating the completion of Ramadan, the holy month of fasting. Marked by prayers, open houses, and delicious traditional food.'
            }],
            '2026-03-22': [{
                title: "Hari Raya Aidilfitri (Day 2)",
                category: 'malay',
                typeLabel: 'Eid al-Fitr',
                icon: '🕌',
                description: 'The second day of Eid, with continued open houses, traditional foods (rendang, ketupat), and spending time with extended family.'
            }],
            '2026-04-03': [{
                title: "Good Friday",
                category: 'national',
                typeLabel: 'Christian Holy Day',
                icon: '✝️',
                description: 'Observance of the crucifixion of Jesus Christ, marked with church services by the Christian community.'
            }],
            '2026-05-01': [{
                title: "Labour Day",
                category: 'national',
                typeLabel: 'National Holiday',
                icon: '🛠️',
                description: 'Hari Pekerja, honoring the contributions of workers nationwide.'
            }],
            '2026-05-27': [{
                title: "Hari Raya Haji",
                category: 'malay',
                typeLabel: 'Eid al-Adha',
                icon: '🐏',
                description: 'The Feast of Sacrifice, commemorating Prophet Ibrahim\'s willingness to sacrifice his son. Marked by prayers and distribution of meat to the needy.'
            }],
            '2026-05-30': [{
                title: "Tadau Kaamatan (Day 1)",
                category: 'indigenous',
                typeLabel: 'Harvest Festival (Sabah)',
                icon: '🌾',
                description: 'Sabahan harvest festival celebrated by the Kadazan-Dusun community to thank the rice spirits. Features traditional dance and delicacies.'
            }],
            '2026-05-31': [
                {
                    title: "Wesak Day",
                    category: 'chinese',
                    typeLabel: 'Buddhist Festival',
                    icon: '☸️',
                    description: 'Commemorates the birth, enlightenment, and passing of Gautama Buddha. Marked by temple prayers and candlelit processions.'
                },
                {
                    title: "Tadau Kaamatan (Day 2)",
                    category: 'indigenous',
                    typeLabel: 'Harvest Festival (Sabah)',
                    icon: '🌾',
                    description: 'The second day of Kaamatan celebrations, continuing the thanksgiving festivities, traditional games, and community gathering.'
                }
            ],
            '2026-06-01': [
                {
                    title: "Agong's Birthday",
                    category: 'national',
                    typeLabel: 'National Holiday',
                    icon: '👑',
                    description: 'Celebrating the official birthday of His Majesty the Yang di-Pertuan Agong, the King of Malaysia.'
                },
                {
                    title: "Hari Gawai (Day 1)",
                    category: 'indigenous',
                    typeLabel: 'Harvest Festival (Sarawak)',
                    icon: '🌾',
                    description: 'Sarawakian harvest festival celebrated by the Dayak people (Iban, Bidayuh) to mark the end of the harvesting season and offer thanks.'
                }
            ],
            '2026-06-02': [{
                title: "Hari Gawai (Day 2)",
                category: 'indigenous',
                typeLabel: 'Harvest Festival (Sarawak)',
                icon: '🍂',
                description: 'Continued Dayak celebrations in longhouses with traditional tuak (rice wine), ngajat dancing, and welcoming visitors.'
            }],
            '2026-06-17': [{
                title: "Awal Muharram",
                category: 'malay',
                typeLabel: 'Islamic New Year',
                icon: '🕌',
                description: 'Marks the beginning of the Islamic New Year (Hijri Calendar). A time for reflection, prayer, and recounting the migration of Prophet Muhammad.'
            }],
            '2026-08-25': [{
                title: "Maulidur Rasul",
                category: 'malay',
                typeLabel: 'Islamic Observance',
                icon: '🕌',
                description: 'Celebrating the birthday of the Prophet Muhammad. Marked by peace marches and Islamic lectures.'
            }],
            '2026-08-31': [{
                title: "National Day (Hari Merdeka)",
                category: 'national',
                typeLabel: 'National Holiday',
                icon: '🇲🇾',
                description: 'Commemorates Malaya\'s independence from British rule in 1957. Celebrated with national parades, patriotism, and fireworks.'
            }],
            '2026-09-16': [{
                title: "Malaysia Day (Hari Malaysia)",
                category: 'national',
                typeLabel: 'National Holiday',
                icon: '🇲🇾',
                description: 'Commemorates the establishment of the Malaysian federation in 1963, uniting Malaya, Sabah, and Sarawak.'
            }],
            '2026-11-08': [{
                title: "Deepavali",
                category: 'indian',
                typeLabel: 'Hindu Festival of Lights',
                icon: '🪔',
                description: 'The Hindu festival of lights, celebrating the victory of light over darkness. Marked by drawing kolam at doorways, family feasts, and sparklers.'
            }],
            '2026-12-25': [{
                title: "Christmas Day",
                category: 'national',
                typeLabel: 'Christian Festival',
                icon: '🎄',
                description: 'Celebrating the birth of Jesus Christ. Marked by church services, Christmas carols, open houses, and exchange of gifts.'
            }]
        };

        let currentYear = 2026;
        let currentMonth = 4; // May (0-indexed)

        const monthNames = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ];

        function renderCalendar(year, month) {
            // Find weekday of 1st day of month
            const firstDayIndex = new Date(year, month, 1).getDay(); // 0 is Sunday, 1 is Monday...
            // Adjust so Monday is 0, Sunday is 6
            const startDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
            
            const totalDays = new Date(year, month + 1, 0).getDate();
            
            // Update Header text
            const monthYearEl = document.getElementById('cal-month-year');
            if (monthYearEl) {
                monthYearEl.textContent = `${monthNames[month]} ${year}`;
            }
            
            const grid = document.getElementById('cal-days-grid');
            if (!grid) return;
            grid.innerHTML = '';
            
            // Add padding days for previous month
            for (let i = 0; i < startDay; i++) {
                const cell = document.createElement('div');
                cell.className = 'cal-day-cell cal-day-empty';
                cell.innerHTML = '&nbsp;';
                grid.appendChild(cell);
            }
            
            // Render active month days
            for (let day = 1; day <= totalDays; day++) {
                const cell = document.createElement('div');
                cell.className = 'cal-day-cell';
                cell.textContent = day;
                
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                // Highlight today's date
                const today = new Date();
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
                if (isToday) {
                    cell.classList.add('cal-day-today');
                }
                
                // Highlight festivals on this date
                const festivals = FESTIVALS_DB[dateStr];
                if (festivals && festivals.length > 0) {
                    const mainCategory = festivals[0].category;
                    cell.classList.add(`cal-day-${mainCategory}`);
                    
                    // Add tiny color dot indicator
                    const dot = document.createElement('span');
                    dot.className = `cal-indicator-dot bg-indicator-${mainCategory}`;
                    cell.appendChild(dot);
                }
                
                // Selection click event
                cell.addEventListener('click', () => {
                    const activeCells = grid.querySelectorAll('.cal-day-active');
                    activeCells.forEach(c => c.classList.remove('cal-day-active'));
                    
                    cell.classList.add('cal-day-active');
                    showDayDetails(dateStr, day, festivals);
                });
                
                grid.appendChild(cell);
            }
            
            // Pre-select day: select today's date if current month/year, else select the 1st of the month
            const today = new Date();
            const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
            const dayToSelect = isCurrentMonth ? today.getDate() : 1;
            
            const cells = grid.querySelectorAll('.cal-day-cell:not(.cal-day-empty)');
            cells.forEach(c => {
                if (parseInt(c.textContent) === dayToSelect) {
                    c.classList.add('cal-day-active');
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayToSelect).padStart(2, '0')}`;
                    showDayDetails(dateStr, dayToSelect, FESTIVALS_DB[dateStr]);
                }
            });
        }

        function showDayDetails(dateStr, day, festivals) {
            const detailPanel = document.getElementById('cal-day-detail');
            if (!detailPanel) return;
            
            const dateParts = dateStr.split('-');
            const dateObj = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
            const formattedDate = dateObj.toLocaleDateString('en-MY', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            if (!festivals || festivals.length === 0) {
                detailPanel.className = "mt-1 p-3.5 rounded-xl border transition-all duration-300 flex flex-col gap-1 bg-slate-900/60 border-slate-700/60 min-h-[96px] justify-center";
                detailPanel.innerHTML = `
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">${formattedDate}</div>
                    <div class="text-sm font-bold text-slate-200 mt-1">Harmonious Day</div>
                    <div class="text-xs text-slate-400 leading-relaxed mt-0.5">No major public holiday or festival listed. A wonderful day to celebrate unity and community!</div>
                `;
                return;
            }
            
            let html = `<div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">${formattedDate}</div>`;
            
            festivals.forEach((f) => {
                let badgeClass = '';
                let borderClass = '';
                let bgGradient = '';
                
                if (f.category === 'malay') {
                    badgeClass = 'culture-pill-malay';
                    borderClass = 'border-emerald-500/20';
                    bgGradient = 'rgba(16, 185, 129, 0.08)';
                } else if (f.category === 'chinese') {
                    badgeClass = 'culture-pill-chinese';
                    borderClass = 'border-red-500/20';
                    bgGradient = 'rgba(239, 68, 68, 0.08)';
                } else if (f.category === 'indian') {
                    badgeClass = 'culture-pill-indian';
                    borderClass = 'border-violet-500/20';
                    bgGradient = 'rgba(139, 92, 246, 0.08)';
                } else if (f.category === 'indigenous') {
                    badgeClass = 'culture-pill-general';
                    borderClass = 'border-amber-500/20';
                    bgGradient = 'rgba(245, 158, 11, 0.08)';
                } else {
                    badgeClass = 'culture-pill-general';
                    borderClass = 'border-blue-500/20';
                    bgGradient = 'rgba(59, 130, 246, 0.08)';
                }
                
                html += `
                    <div class="flex flex-col gap-1 p-2.5 rounded-lg border ${borderClass} mb-2 last:mb-0" style="background: ${bgGradient};">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-white flex items-center gap-1.5">
                                <span>${f.icon}</span> ${f.title}
                            </span>
                            <span class="culture-pill ${badgeClass} text-[9px] scale-90 origin-right py-0 px-2">${f.typeLabel}</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed mt-1">${f.description}</p>
                    </div>
                `;
            });
            
            // Style the panel border to match first holiday category
            const mainCategory = festivals[0].category;
            detailPanel.className = `mt-1 p-3.5 rounded-xl border transition-all duration-300 flex flex-col gap-1 bg-slate-900/80 min-h-[96px] justify-center`;
            
            if (mainCategory === 'malay') detailPanel.classList.add('border-emerald-500/40');
            else if (mainCategory === 'chinese') detailPanel.classList.add('border-red-500/40');
            else if (mainCategory === 'indian') detailPanel.classList.add('border-violet-500/40');
            else if (mainCategory === 'indigenous') detailPanel.classList.add('border-amber-500/40');
            else detailPanel.classList.add('border-blue-500/40');
            
            detailPanel.innerHTML = html;
        }

        // Initialize and bind buttons
        setTimeout(() => {
            const prevBtn = document.getElementById('cal-prev-btn');
            const nextBtn = document.getElementById('cal-next-btn');
            
            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', () => {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
                
                nextBtn.addEventListener('click', () => {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
            }
            
            // Set dynamic default to current system date (if in 2026)
            const today = new Date();
            if (today.getFullYear() === 2026) {
                currentMonth = today.getMonth();
            } else {
                currentMonth = 4; // Fallback to May 2026 for demonstration
            }
            
            renderCalendar(currentYear, currentMonth);
        }, 300);

        // Modal functions for "Read More" cultural events
        window.openEventDetailModal = function(title, description, imgUrl, dateStr, pillLabel, pillClass, bgClass, icon) {
            const modal = document.getElementById('culture-detail-modal');
            if (!modal) return;
            const modalBox = modal.querySelector('div:not(.absolute)');
            const modalTitle = document.getElementById('modal-event-title');
            const modalDesc = document.getElementById('modal-event-desc');
            const modalImg = document.getElementById('modal-event-img');
            const modalPlaceholder = document.getElementById('modal-event-placeholder');
            const modalIcon = document.getElementById('modal-event-placeholder-icon');
            const modalPill = document.getElementById('modal-event-pill');
            const modalDate = document.getElementById('modal-event-date');
            
            modalTitle.textContent = title;
            modalDesc.textContent = description;
            modalDate.textContent = dateStr;
            
            // Set up pill badge
            modalPill.className = `culture-pill ${pillClass} text-xs`;
            modalPill.textContent = pillLabel;
            
            // Set up image or placeholder
            if (imgUrl) {
                modalImg.src = imgUrl;
                modalImg.classList.remove('hidden');
                modalPlaceholder.classList.add('hidden');
            } else {
                modalImg.classList.add('hidden');
                modalPlaceholder.classList.remove('hidden');
                
                // Clear existing bg classes and set the new one
                modalPlaceholder.className = `w-full h-full ${bgClass} flex items-center justify-center relative`;
                modalIcon.textContent = icon;
            }
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Lock background scroll
            
            // Animate box in
            setTimeout(() => {
                modalBox.classList.remove('scale-95', 'opacity-0');
                modalBox.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        window.closeEventDetailModal = function() {
            const modal = document.getElementById('culture-detail-modal');
            if (!modal) return;
            const modalBox = modal.querySelector('div:not(.absolute)');
            
            modalBox.classList.remove('scale-100', 'opacity-100');
            modalBox.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = ''; // Unlock scroll
            }, 200);
        }
    </script>
    @endscript
</div>
