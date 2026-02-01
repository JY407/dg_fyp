<?php

use Livewire\Volt\Component;
use App\Models\EmergencyAlert;
use App\Models\SecurityDuty;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $triggered = false;

    public function with()
    {
        return [
            'duties' => SecurityDuty::whereDate('date', now()->toDateString())
                ->orderBy('shift')
                ->get()
        ];
    }

    public function triggerEmergency()
    {
        // ... (Maintained)
        EmergencyAlert::create([
            'user_id' => Auth::id(),
            'type' => 'Emergency',
            'message' => 'Emergency Button Triggered',
            'status' => 'pending',
        ]);

        $this->triggered = true;
        session()->flash('message', 'Emergency Alert Sent! Management has been notified.');
    }
}; ?>
...


<div class="min-h-screen relative overflow-hidden bg-slate-100 dark:bg-zinc-950 font-sans">

    <!-- Giant Watermark Background (Faded) -->
    <div
        class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none z-0 opacity-[0.03]">
        <span class="text-[20vw] font-black uppercase tracking-tighter text-red-600 scale-150 select-none">
            Emergency
        </span>
    </div>

    <!-- Content Container -->
    <div class="relative z-10 max-w-5xl mx-auto px-6 py-12 flex flex-col items-center">

        <!-- Header -->
        <div class="text-center mb-16 space-y-2">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-zinc-100 uppercase tracking-tight">
                Emergency Contact
            </h1>
            <p class="text-xl md:text-2xl text-slate-500 font-light tracking-wide">
                Please call contact us immediately
            </p>
        </div>

        <!-- The Big Physical Button -->
        <div class="mb-20 relative group">
            <div
                class="absolute inset-0 bg-red-500/20 rounded-full blur-[60px] group-hover:bg-red-500/30 transition-all duration-500">
            </div>

            <button wire:click="triggerEmergency"
                class="relative w-64 h-64 shrink-0 rounded-full flex items-center justify-center transition-transform active:scale-95 duration-100 ease-out shadow-2xl"
                style="background: radial-gradient(circle at 30% 30%, #ef4444, #b91c1c); box-shadow: 0 20px 50px rgba(220, 38, 38, 0.5), inset 0 4px 8px rgba(255,255,255,0.3);">

                <!-- Inner Ring -->
                <div class="w-56 h-56 shrink-0 rounded-full flex items-center justify-center border-4 border-red-900/10"
                    style="background: radial-gradient(circle at 50% 50%, #dc2626, #991b1b);">

                    @if($triggered)
                        <div class="flex flex-col items-center animate-pulse">
                            <svg class="w-16 h-16 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-white font-bold tracking-widest text-lg">SENT</span>
                        </div>
                    @else
                        <span class="text-4xl font-extrabold text-white tracking-wider drop-shadow-md">
                            EMERGENCY
                        </span>
                    @endif
                </div>
            </button>

            <div class="text-center mt-8">
                <p class="text-zinc-900 dark:text-zinc-200 font-bold uppercase tracking-widest text-sm">
                    Tap to report an emergency
                </p>
                <p class="text-red-500 text-xs font-semibold mt-1">
                    Penalty imposed on false alarm
                </p>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-12 px-6 py-4 bg-green-500 text-white rounded-lg font-bold shadow-lg animate-bounce">
                {{ session('message') }}
            </div>
        @endif

        <!-- Who Duty Today Section -->
        <div class="w-full relative">
            <!-- Section Header -->
            <div class="flex items-end gap-4 mb-8 border-b-2 border-zinc-100 pb-4">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Who Duty Today</h2>
                    <p class="text-zinc-500">Security Personnel</p>
                </div>
            </div>

            <!-- ID Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Placeholder Data -->
                @forelse($duties as $duty)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow flex">
                        <!-- ID Photo Placeholder -->
                        <div class="w-32 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border-r border-zinc-100 dark:border-zinc-800">
                            <div class="text-4xl font-black text-zinc-300 select-none">
                                {{ substr($duty->guard_name, 0, 1) }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 p-5 flex flex-col justify-between">
                            <div>
                                <h3 class="font-black text-lg text-zinc-900 dark:text-zinc-100 uppercase leading-none mb-1">
                                    {{ $duty->guard_name }}
                                </h3>
                                <span class="inline-block px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs font-bold uppercase rounded-sm mb-3">
                                    {{ $duty->shift }} Shift
                                </span>

                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-mono">{{ $duty->location }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-zinc-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($duty->date)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-zinc-500">
                        No security personnel assigned for today.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>