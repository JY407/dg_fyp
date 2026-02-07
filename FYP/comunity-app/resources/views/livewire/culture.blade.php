<?php

use Livewire\Volt\Component;
use App\Models\CultureEvent;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with()
    {
        return [
            'events' => CultureEvent::orderBy('event_date', 'desc')->get()
        ];
    }
}; ?>

<div class="py-12 bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-500 mb-4 animate-fade-in-down">
                Malaysia Culture & History
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Discover the rich heritage, historical events, and diverse culture that make Malaysia unique.
            </p>
        </div>

        <!-- Religious & Cultural Calendar Section -->
        <div class="mb-16 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Muslim Prayer Times -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg relative overflow-hidden group hover:border-teal-500/50 transition-all duration-300">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-teal-400">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path>
                        <path d="M12 6a4 4 0 0 0-4 4v4"></path>
                        <path d="M16 10a4 4 0 0 0-4-4"></path>
                        <path d="M12 14h.01"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-teal-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 10c0-3.9 0-6.9-4-6.9a6 6 0 0 0-6 6c0 3.8 0 7 4 7h6z"/><path d="M9 10a3 3 0 0 1 3-3"/><path d="M19 21v-2"/><path d="M7 21v-2"/><path d="M19 21h-2"/><path d="M7 21h2"/><path d="M15 21v-4a2 2 0 0 0-2-2"/></svg>
                    </span>
                    Islamic Prayer Times
                </h2>
                <div class="space-y-4" id="prayer-times-container" wire:ignore>
                    <div class="animate-pulse flex space-x-4">
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                            <div class="space-y-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="h-16 bg-gray-700 rounded"></div>
                                    <div class="h-16 bg-gray-700 rounded"></div>
                                    <div class="h-16 bg-gray-700 rounded"></div>
                                    <div class="h-16 bg-gray-700 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500 text-right">
                    Method: JAKIM (Kuala Lumpur)
                </div>
            </div>

            <!-- Buddhist/Chinese Lunar Calendar -->
            <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg relative overflow-hidden group hover:border-teal-500/50 transition-all duration-300">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-teal-400">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-teal-400">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/></svg>
                    </span>
                    Chinese / Lunar Date
                </h2>
                <div class="flex flex-col items-center justify-center h-[calc(100%-4rem)] pb-4" id="lunar-date-container" wire:ignore>
                    <div class="animate-pulse flex flex-col items-center space-y-3 w-full">
                         <div class="h-16 w-16 bg-gray-700 rounded-full"></div>
                         <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                         <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts for Calendar -->
        @assets
        <script src="https://cdn.jsdelivr.net/npm/lunar-javascript@1.6.13/lunar.js"></script>
        @endassets
        
        @script
        <script>
                // Flash message for user awareness (optional)
                console.log('Initializing Cultural Calendar Widgets...');

                // 1. Fetch Prayer Times (Aladhan API)
                // Method 17 is JAKIM (Jabatan Kemajuan Islam Malaysia)
                const today = new Date();
                const dateStr = `${today.getDate()}-${today.getMonth() + 1}-${today.getFullYear()}`;
                
                fetch(`https://api.aladhan.com/v1/timingsByCity?city=Kuala+Lumpur&country=Malaysia&method=17`)
                    .then(res => res.json())
                    .then(data => {
                        if(data && data.data && data.data.timings) {
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
                                // Simple time formatting (remove timezone if present)
                                let time = p.time.split(' ')[0]; 
                                html += `
                                    <div class="bg-gray-700/30 rounded-lg p-2 text-center border border-gray-600 hover:bg-gray-700/50 transition-colors">
                                        <div class="text-teal-400 text-[10px] uppercase font-bold tracking-wider mb-1">${p.name}</div>
                                        <div class="text-lg font-bold text-white">${time}</div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            
                            // Add Hijri Date
                            if (data.data.date && data.data.date.hijri) {
                                const h = data.data.date.hijri;
                                html += `
                                    <div class="mt-4 pt-3 border-t border-gray-700 flex justify-between items-center text-sm text-gray-400">
                                        <span>Hijri Date:</span>
                                        <span class="text-white font-medium">${h.day} ${h.month.en} ${h.year}</span>
                                    </div>
                                `;
                            }

                            document.getElementById('prayer-times-container').innerHTML = html;
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching prayer times:', err);
                        document.getElementById('prayer-times-container').innerHTML = '<div class="text-red-400 text-sm text-center">Failed to load prayer times. Check connection.</div>';
                    });

                // 2. Calculate Lunar Date
                // We utilize the window.Solar from lunar-javascript
                try {
                    // Check if library loaded
                    setTimeout(() => {
                        if (window.Solar) {
                            const solar = Solar.fromDate(new Date());
                            const lunar = solar.getLunar();
                            
                            const day = lunar.getDay();
                            const month = lunar.getMonth();
                            // English translations usually need a map, but the library might have .toString() or .toFullString()
                            // However, let's use the Chinese terms then a helper for "1st/15th" logic.
                            
                            const dayChinese = lunar.getDayInChinese();
                            const monthChinese = lunar.getMonthInChinese();
                            
                            const isFirst = (day === 1);
                            const isFifteenth = (day === 15);
                            
                            let html = `
                                <div class="text-center">
                                    <div class="text-6xl font-extrabold text-white mb-2 tracking-tight">${day}</div>
                                    <div class="text-xl text-teal-300 font-medium mb-4">Lunar Day</div>
                                    <div class="text-lg text-gray-300 bg-gray-900/50 px-4 py-2 rounded-full inline-block border border-gray-700">
                                        ${monthChinese} Month · ${dayChinese}
                                    </div>
                                </div>
                            `;

                            if (isFirst || isFifteenth) {
                                html += `
                                    <div class="mt-6 px-4 py-3 bg-yellow-500/10 text-yellow-500 rounded-xl border border-yellow-500/20 animate-pulse flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M3 21v-2a7 7 0 0 1 7-7h4a7 7 0 0 1 7 7v2"/></svg>
                                        <span class="font-bold">Vegetarian Day (Hari Vegetarian)</span>
                                    </div>
                                `;
                            } else {
                                // Calculate days to next 1st or 15th
                                let msg = "";
                                if (day < 15) {
                                    msg = `Next: 15th (${15 - day} days left)`;
                                } else {
                                    // Month days varies (29 or 30), simple approx or check library
                                    // For simplicity in UI logic:
                                    msg = "Next: 1st (New Moon)";
                                }
                                html += `
                                    <div class="mt-6 text-sm text-gray-500 flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        ${msg}
                                    </div>
                                `;
                            }
                            
                            document.getElementById('lunar-date-container').innerHTML = html;
                        } else {
                            document.getElementById('lunar-date-container').innerHTML = '<div class="text-gray-400 text-sm">Loading library...</div>';
                        }
                    }, 500); // Small delay to ensure script execution if async
                } catch (e) {
                    console.error('Lunar Error:', e);
                    document.getElementById('lunar-date-container').innerHTML = '<div class="text-red-400 text-sm">Error loading Lunar Date</div>';
                }
        </script>
        @endscript

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <div class="group relative bg-gray-800 rounded-2xl overflow-hidden border border-gray-700 hover:border-teal-500/50 transition-all duration-300 hover:shadow-[0_0_30px_rgba(45,212,191,0.15)]">
                    <!-- Image -->
                    <div class="h-64 overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-60 z-10"></div>
                        @if($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </div>
                        @endif
                        
                        <!-- Date Badge -->
                        <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-md border border-white/10 px-3 py-1 rounded-full text-sm font-semibold text-teal-300 z-20">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 relative z-20 -mt-12">
                        <div class="bg-gray-800/80 backdrop-blur-xl border border-white/5 p-6 rounded-xl shadow-lg">
                            <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-teal-400 transition-colors">{{ $event->title }}</h3>
                            <p class="text-gray-400 leading-relaxed text-sm line-clamp-4">
                                {{ $event->description }}
                            </p>
                            
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <button class="text-sm text-teal-400 font-semibold flex items-center gap-2 hover:gap-3 transition-all">
                                    Read More
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-300 mb-2">No Content Yet</h3>
                    <p class="text-gray-500 max-w-md">Our history is being written. Check back soon for updates on Malaysia's rich culture and history.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
