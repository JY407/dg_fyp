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

            {{-- Chinese / Lunar Calendar --}}
            <div class="bg-slate-800 rounded-2xl shadow-sm border border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-3"
                    style="background: linear-gradient(135deg, #422006, #78350f);">
                    <div class="w-9 h-9 rounded-xl bg-yellow-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Chinese / Lunar Calendar</h2>
                        <p class="text-xs text-gray-400">Current lunar date &amp; vegetarian days</p>
                    </div>
                </div>
                <div class="p-5 flex items-center justify-center min-h-36" id="lunar-date-container" wire:ignore>
                    <div class="animate-pulse flex flex-col items-center gap-3 w-full">
                        <div class="w-16 h-16 bg-gray-700 rounded-full"></div>
                        <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-700 rounded w-3/4"></div>
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
                            <div class="p-5">
                                {{-- Culture pill --}}
                                <div class="mb-2">
                                    <span class="culture-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors line-clamp-1">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-400 leading-relaxed line-clamp-3">{{ $event->description }}</p>
                                <div class="mt-3 text-xs text-indigo-400 font-semibold">
                                    {{ $isToday ? 'Happening today!' : 'In ' . \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
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
                            <div class="p-4">
                                <div class="mb-1">
                                    <span class="culture-pill {{ $pillClass2 }}" style="opacity:.65;">{{ $pillLabel2 }}</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-400 mb-1 group-hover:text-slate-300 transition-colors line-clamp-1">{{ $event->title }}</h3>
                                <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $event->description }}</p>
                                <div class="mt-2 text-xs text-slate-600">{{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif
    </div>

    {{-- Scripts --}}
    @assets
    <script src="https://cdn.jsdelivr.net/npm/lunar-javascript@1.6.13/lunar.js"></script>
    @endassets

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

        // Lunar Date
        setTimeout(() => {
            try {
                if (window.Solar) {
                    const solar = Solar.fromDate(new Date());
                    const lunar = solar.getLunar();
                    const day = lunar.getDay();
                    const dayChinese = lunar.getDayInChinese();
                    const monthChinese = lunar.getMonthInChinese();
                    const isSpecial = day === 1 || day === 15;

                    let html = `
                        <div class="text-center w-full">
                            <div class="text-5xl font-extrabold text-gray-900 mb-1">${day}</div>
                            <div class="text-sm font-semibold text-yellow-600 mb-3">Lunar Day</div>
                            <div class="inline-block bg-yellow-50 text-yellow-700 font-medium text-sm px-4 py-1.5 rounded-full border border-yellow-200">
                                ${monthChinese} Month · ${dayChinese}
                            </div>
                            ${isSpecial ? `
                            <div class="mt-4 px-4 py-2.5 bg-yellow-50 text-yellow-700 rounded-xl border border-yellow-200 text-sm font-semibold flex items-center justify-center gap-2">
                                🌿 Vegetarian Day (Hari Vegetarian)
                            </div>` : `<div class="mt-4 text-xs text-gray-400">${day < 15 ? 'Next vegetarian day: 15th (' + (15-day) + ' days)' : 'Next: 1st (New Moon)'}</div>`}
                        </div>`;
                    document.getElementById('lunar-date-container').innerHTML = html;
                }
            } catch (e) {
                document.getElementById('lunar-date-container').innerHTML = '<p class="text-sm text-red-400 text-center">Error loading Lunar Date</p>';
            }
        }, 500);
    </script>
    @endscript
</div>
