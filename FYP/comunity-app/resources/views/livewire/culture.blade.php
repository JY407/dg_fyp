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
    </style>
    @endpush

    {{-- Page Header --}}
    <div class="px-6 pt-8 pb-6">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m12 3-1.9 5.8a2 2 0 0 1-1.287 1.288L3 12l5.8 1.9a2 2 0 0 1 1.288 1.287L12 21l1.9-5.8a2 2 0 0 1 1.287-1.288L21 12l-5.8-1.9a2 2 0 0 1-1.288-1.287Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Malaysia Culture & History</h1>
                <p class="text-gray-400 text-sm mt-0.5">Discover heritage, traditions, and cultural events in our community.</p>
            </div>
        </div>
    </div>

    <div class="px-6 pb-10">

        {{-- Calendar Widgets Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

            {{-- Prayer Times --}}
            <div class="rounded-2xl border border-gray-700 overflow-hidden" style="background:#1e293b;">
                <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-3"
                    style="background: linear-gradient(135deg, #064e3b, #047857);"> {{-- Darker gradient --}}
                    <div class="w-9 h-9 rounded-xl bg-emerald-900 flex items-center justify-center"> {{-- Darker bg --}}
                        <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> {{-- Lighter icon --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Islamic Prayer Times</h2> {{-- Lighter text --}}
                        <p class="text-xs text-gray-400">JAKIM Method — Kuala Lumpur</p> {{-- Lighter text --}}
                    </div>
                </div>
                <div class="p-5">
                    <div id="prayer-times-container">
                        <div class="grid grid-cols-3 gap-3 animate-pulse">
                            @for($i = 0; $i < 6; $i++)
                                <div class="h-16 bg-gray-700 rounded-xl"></div> {{-- Darker placeholder --}}
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chinese / Lunar Calendar --}}
            <div class="bg-slate-800 rounded-2xl shadow-sm border border-gray-700 overflow-hidden"> {{-- Darker card bg and border --}}
                <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-3"
                    style="background: linear-gradient(135deg, #422006, #78350f);"> {{-- Darker gradient --}}
                    <div class="w-9 h-9 rounded-xl bg-yellow-900 flex items-center justify-center"> {{-- Darker bg --}}
                        <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> {{-- Lighter icon --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Chinese / Lunar Calendar</h2> {{-- Lighter text --}}
                        <p class="text-xs text-gray-400">Current lunar date & vegetarian days</p> {{-- Lighter text --}}
                    </div>
                </div>
                <div class="p-5 flex items-center justify-center min-h-36" id="lunar-date-container" wire:ignore>
                    <div class="animate-pulse flex flex-col items-center gap-3 w-full">
                        <div class="w-16 h-16 bg-gray-700 rounded-full"></div> {{-- Darker placeholder --}}
                        <div class="h-4 bg-gray-700 rounded w-1/2"></div> {{-- Darker placeholder --}}
                        <div class="h-4 bg-gray-700 rounded w-3/4"></div> {{-- Darker placeholder --}}
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
                        @endphp
                        <div class="rounded-2xl border overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group relative"
                            style="background:#1e293b; border-color: {{ $isToday ? '#6366f1' : 'rgba(71,85,105,.5)' }};">
                            {{-- Image --}}
                            <div class="h-48 overflow-hidden relative bg-gray-800">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}"
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-800 to-indigo-900">
                                        <svg class="w-12 h-12 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
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
                        <div class="rounded-2xl border border-slate-700/40 overflow-hidden transition-all duration-200 group opacity-70 hover:opacity-90"
                            style="background:#1a2537;">
                            {{-- Image --}}
                            <div class="h-40 overflow-hidden relative bg-gray-800">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}"
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-900 to-slate-800">
                                        <svg class="w-10 h-10 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-slate-800/90 text-slate-400 text-xs font-bold px-3 py-1.5 rounded-full border border-slate-700">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                </div>
                            </div>
                            {{-- Content --}}
                            <div class="p-4">
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
