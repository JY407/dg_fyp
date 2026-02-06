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
