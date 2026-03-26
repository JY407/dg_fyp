<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\CommunityService;

new #[Layout('layouts.app')] class extends Component {
    public function with()
    {
        return [
            'services' => CommunityService::orderBy('created_at', 'desc')->get()
        ];
    }
}; ?>

<div class="min-h-screen" style="background:#0f172a;">
    @push('styles')
    <style>
        .service-card { background:rgba(30,41,59,.7); border:1px solid rgba(71,85,105,.35); transition:all .2s ease; }
        .service-card:hover { background:rgba(30,41,59,.95); border-color:rgba(99,102,241,.4); transform:translateY(-3px); box-shadow:0 20px 40px rgba(0,0,0,.4); }
        .badge { font-size:11px; font-weight:700; padding:3px 10px; border-radius:9999px; }
    </style>
    @endpush

    <div class="px-6 pt-8 pb-5">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-900/40 shrink-0"
                style="background:linear-gradient(135deg,#3b82f6,#6366f1);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight leading-none">{{ __('app.services_title') }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('app.services_subtitle') }}</p>
            </div>
        </div>
    </div>

    <div class="px-6 pb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($services as $service)
                @php
                    $colorMap = [
                        'Weekly'  => ['bar'=>'#3b82f6','badge_bg'=>'rgba(59,130,246,.15)','badge_border'=>'rgba(59,130,246,.3)','badge_text'=>'#93c5fd','icon_bg'=>'rgba(59,130,246,.12)','icon_text'=>'#60a5fa'],
                        'Monthly' => ['bar'=>'#8b5cf6','badge_bg'=>'rgba(139,92,246,.15)','badge_border'=>'rgba(139,92,246,.3)','badge_text'=>'#c4b5fd','icon_bg'=>'rgba(139,92,246,.12)','icon_text'=>'#a78bfa'],
                    ];
                    $c = $colorMap[$service->frequency] ?? ['bar'=>'#10b981','badge_bg'=>'rgba(16,185,129,.15)','badge_border'=>'rgba(16,185,129,.3)','badge_text'=>'#6ee7b7','icon_bg'=>'rgba(16,185,129,.12)','icon_text'=>'#34d399'];
                @endphp
                <div class="service-card rounded-2xl overflow-hidden">
                    {{-- Color top bar --}}
                    <div class="h-1" style="background:{{ $c['bar'] }};"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                                style="background:{{ $c['icon_bg'] }}; border:1px solid {{ $c['badge_border'] }};">
                                <svg class="w-5 h-5" style="color:{{ $c['icon_text'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            @if($service->contact_number)
                                <a href="tel:{{ $service->contact_number }}"
                                    class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all"
                                    style="background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.25);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Call
                                </a>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-white mb-0.5">{{ $service->service_name }}</h3>
                        <p class="text-sm text-slate-400 mb-4">{{ $service->provider_name }}</p>

                        <div class="flex flex-wrap gap-2">
                            <span class="badge" style="background:{{ $c['badge_bg'] }}; color:{{ $c['badge_text'] }}; border:1px solid {{ $c['badge_border'] }};">
                                {{ $service->frequency }}
                            </span>
                            @if($service->day_of_week)
                                <span class="badge" style="background:rgba(71,85,105,.25); color:#94a3b8; border:1px solid rgba(71,85,105,.35);">
                                    {{ $service->day_of_week }}
                                </span>
                            @endif
                            @if($service->time_slot)
                                <span class="badge flex items-center gap-1" style="background:rgba(71,85,105,.25); color:#94a3b8; border:1px solid rgba(71,85,105,.35);">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $service->time_slot }}
                                </span>
                            @endif
                        </div>

                        @if($service->description)
                            <div class="mt-4 pt-4 border-t border-slate-700/50">
                                <p class="text-sm text-slate-400 leading-relaxed">{{ $service->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-24">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 shadow-xl"
                        style="background:rgba(59,130,246,.1); border:1px solid rgba(59,130,246,.2);">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-300 mb-1">{{ __('app.services_no_results') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('app.services_subtitle') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>