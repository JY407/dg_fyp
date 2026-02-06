<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\CommunityService;

new #[Layout('layouts.app')] class extends Component {
    public function with()
    {
        return [
            'services' => CommunityService::where('status', true)->orderBy('created_at', 'desc')->get()
        ];
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="mb-8">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-500">
            Community Services
        </h2>
        <p class="text-gray-400 mt-2">Scheduled maintenance and services for our community.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
            <div
                class="glass-card p-6 rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)] hover:bg-[rgba(255,255,255,0.05)] transition-all hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-start justify-between">
                    <div
                        class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                            </path>
                        </svg>
                    </div>
                    @if($service->contact_number)
                        <a href="tel:{{ $service->contact_number }}"
                            class="text-xs font-bold px-2 py-1 rounded bg-green-500/10 text-green-400 hover:bg-green-500/20 border border-green-500/20 transition-colors">
                            Call Provider
                        </a>
                    @endif
                </div>

                <h3 class="text-xl font-bold text-white mb-1">{{ $service->service_name }}</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $service->provider_name }}</p>

                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-gray-300">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>{{ $service->frequency }}</span>
                        @if($service->day_of_week)
                            <span class="text-gray-500">•</span>
                            <span>{{ $service->day_of_week }}</span>
                        @endif
                    </div>

                    @if($service->time_slot)
                        <div class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>{{ $service->time_slot }}</span>
                        </div>
                    @endif
                </div>

                @if($service->description)
                    <div class="mt-4 pt-4 border-t border-[rgba(255,255,255,0.05)]">
                        <p class="text-sm text-gray-400 leading-relaxed">{{ $service->description }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div
                    class="w-16 h-16 bg-[rgba(255,255,255,0.05)] rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">No Services Scheduled</h3>
                <p class="text-gray-400">There are currently no community services listed.</p>
            </div>
        @endforelse
    </div>
</div>