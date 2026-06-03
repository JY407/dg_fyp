<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\CommunityService;
use App\Models\ServiceBooking;

new #[Layout('layouts.app')] class extends Component {
    public $showBookingModal = false;
    public $selectedServiceId = null;
    public $selectedServiceName = '';
    public $booking_date = '';
    public $requested_time = '';
    public $notes = '';

    public function mount()
    {
        $this->booking_date = now()->addDays(1)->format('Y-m-d');
    }

    public function with()
    {
        $myBookings = [];
        if (auth()->check()) {
            $myBookings = auth()->user()->serviceBookings()->with('communityService')->orderBy('created_at', 'desc')->get();
        }

        return [
            'services' => CommunityService::orderBy('created_at', 'desc')->get(),
            'myBookings' => $myBookings
        ];
    }

    public function openBookingModal($id, $name)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }
        $this->selectedServiceId = $id;
        $this->selectedServiceName = $name;
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->reset(['selectedServiceId', 'selectedServiceName', 'requested_time', 'notes']);
        $this->booking_date = now()->addDays(1)->format('Y-m-d');
    }

    public function submitBooking()
    {
        $this->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'requested_time' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        auth()->user()->serviceBookings()->create([
            'community_service_id' => $this->selectedServiceId,
            'booking_date' => $this->booking_date,
            'requested_time' => $this->requested_time,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        $this->closeBookingModal();
        session()->flash('success', 'Service booked successfully! We will coordinate with the provider.');
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

    @if (session()->has('success'))
        <div class="px-6 mb-6">
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-2xl flex items-center shadow-[0_0_15px_rgba(74,222,128,0.1)] max-w-4xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="px-6 pb-10 flex flex-row gap-8 items-start">
        <div class="flex-1 w-full">
            <div class="grid grid-cols-2 gap-5">
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
                        <div class="flex flex-col items-end gap-2">
                            @if($service->contact_number)
                                <a href="tel:{{ $service->contact_number }}"
                                    class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all hover:bg-emerald-500/20"
                                    style="background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.25);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Call
                                </a>
                            @endif
                            @if($service->frequency === 'Ad-hoc' || str_contains(strtolower($service->service_name), 'solar'))
                                <button wire:click="openBookingModal({{ $service->id }}, '{{ addslashes($service->service_name) }}')"
                                    class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all hover:bg-blue-500/20"
                                    style="background:rgba(59,130,246,.12); color:#60a5fa; border:1px solid rgba(59,130,246,.25);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Book Now
                                </button>
                            @endif
                        </div>
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
                </div>
            @endforelse
            </div>
        </div>

        <!-- Sidebar for Your Bookings -->
        @auth
        <div class="w-80 shrink-0">
            <div class="bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-gray-700/50 p-6 relative overflow-hidden group shadow-lg">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-16 -mt-16 transition-all group-hover:bg-blue-500/20"></div>
                
                <h3 class="font-bold text-white text-lg mb-6 flex items-center gap-2 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Your Service Bookings
                </h3>
                
                <div class="space-y-4 relative z-10">
                    @forelse($myBookings as $booking)
                        <div class="bg-gray-900/50 border border-gray-700/50 rounded-xl p-4 hover:border-blue-500/30 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-gray-200 font-bold text-sm">{{ $booking->communityService->service_name }}</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded
                                    {{ $booking->status === 'approved' ? 'bg-green-500/20 text-green-400 border-green-500/30' : ($booking->status === 'rejected' ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30') }} border">
                                    {{ $booking->status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                            </div>
                            @if($booking->requested_time)
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $booking->requested_time }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 bg-gray-900/50 border border-gray-700/50 rounded-xl p-6 text-center border-dashed">
                            No service bookings yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endauth
    </div>

    {{-- Booking Modal --}}
    @if($showBookingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-4">
            <div class="relative w-full max-w-md bg-gray-900 rounded-3xl shadow-2xl border border-white/10 flex flex-col max-h-[90vh]">

                {{-- Gradient Header --}}
                <div class="relative flex-shrink-0 bg-gradient-to-r from-blue-700 via-purple-700 to-indigo-800 px-8 pt-7 pb-6 rounded-t-3xl overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative flex justify-between items-start">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 border border-white/20 text-blue-200 mb-3 uppercase tracking-wider">
                                🛠️ Service Request
                            </span>
                            <h3 class="text-2xl font-black text-white tracking-tight">Book Service</h3>
                            <p class="text-sm text-blue-200/80 mt-1">{{ $selectedServiceName }}</p>
                        </div>
                        <button wire:click="closeBookingModal" class="text-white/60 hover:text-white bg-white/10 hover:bg-white/20 p-2.5 rounded-full transition-all duration-300 border border-white/10 hover:rotate-90 shrink-0 ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Form Body --}}
                <div class="overflow-y-auto flex-1">
                    <form wire:submit="submitBooking" class="p-7 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Preferred Date <span class="text-red-400">*</span></label>
                            <input wire:model="booking_date" type="date" required
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" style="color-scheme:dark">
                            @error('booking_date') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Preferred Time / Slot <span class="text-gray-500 font-normal text-xs">(Optional)</span></label>
                            <input wire:model="requested_time" type="text" placeholder="e.g. Morning, 2:00 PM"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            @error('requested_time') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Additional Notes <span class="text-gray-500 font-normal text-xs">(Optional)</span></label>
                            <textarea wire:model="notes" rows="3" placeholder="Tell us exactly what you need help with..."
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none"></textarea>
                            @error('notes') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2 border-t border-gray-700/60">
                            <button type="button" wire:click="closeBookingModal"
                                class="flex-1 px-5 py-3 bg-gray-800 hover:bg-gray-700 border border-gray-600 text-gray-300 font-semibold rounded-xl transition-all text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-5 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold rounded-xl shadow-lg shadow-purple-900/40 transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                                <svg wire:loading wire:target="submitBooking" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <svg wire:loading.remove wire:target="submitBooking" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                <span wire:loading.remove wire:target="submitBooking">Confirm Request</span>
                                <span wire:loading wire:target="submitBooking">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
