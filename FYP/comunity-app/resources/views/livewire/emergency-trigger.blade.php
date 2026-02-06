<?php

use Livewire\Volt\Component;
use App\Models\EmergencyAlert;
use App\Models\SecurityDuty;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\Computed;

new class extends Component {
    public $triggered = false;

    #[Computed]
    public function duties()
    {
        return SecurityDuty::whereDate('date', now()->toDateString())
            ->orderBy('shift')
            ->get();
    }

    public function triggerEmergency()
    {
        EmergencyAlert::create([
            'user_id' => Auth::id(),
            'type' => 'Emergency',
            'message' => 'Emergency Button Triggered',
            'status' => 'pending',
        ]);

        $this->triggered = true;
        session()->flash('message', 'Emergency Alert Sent! Management has been notified.');

        // Reset triggered status after a delay if needed, 
        // but typically user should know it's SENT until refreshed or handled
    }
}; ?>

<div class="min-h-screen relative overflow-hidden font-sans flex flex-col items-center justify-center py-10"
    style="background: radial-gradient(circle at center, #1a1c2e 0%, #000000 100%);">

    <!-- Ambient Background Effects -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-red-900/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-900/10 rounded-full blur-[100px]"></div>
    </div>

    <!-- Giant Watermark Background -->
    <div
        class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none z-0 opacity-[0.05]">
        <span class="text-[25vw] font-black uppercase tracking-tighter text-red-600 scale-150 select-none blur-sm">
            SOS
        </span>
    </div>

    <!-- Content Container -->
    <div class="relative z-10 w-full max-w-4xl px-6 flex flex-col items-center gap-12">

        <!-- Header -->
        <div class="text-center space-y-4 animate-fade-in-up">
            <h1
                class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-b from-white to-white/50 uppercase tracking-tight drop-shadow-sm">
                Emergency
            </h1>
            <p class="text-xl text-white/60 font-light tracking-widest uppercase">
                Immediate Assistance Required?
            </p>
        </div>

        <!-- The Big Physical Button -->
        <div class="relative group animate-pulse-slow">
            <!-- Glow Effect -->
            <div
                class="absolute inset-0 bg-red-600/30 rounded-full blur-[80px] group-hover:bg-red-500/40 transition-all duration-500">
            </div>

            <button wire:click="triggerEmergency"
                class="relative w-72 h-72 shrink-0 rounded-full flex items-center justify-center transition-all active:scale-95 duration-200 ease-out group"
                style="
                    background: radial-gradient(145deg, #ef4444, #991b1b);
                    box-shadow: 
                        0 20px 60px rgba(220, 38, 38, 0.4),
                        inset 0 4px 20px rgba(255,255,255,0.4),
                        inset 0 -10px 20px rgba(0,0,0,0.3);
                ">

                <!-- Inner Ring -->
                <div class="w-60 h-60 shrink-0 rounded-full flex items-center justify-center border-4 border-red-900/20 shadow-inner"
                    style="background: radial-gradient(circle at 40% 40%, #dc2626, #7f1d1d);">

                    @if($triggered)
                        <div class="flex flex-col items-center animate-bounce-slow">
                            <svg class="w-20 h-20 text-white mb-2 filter drop-shadow-lg" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-white font-black tracking-widest text-2xl drop-shadow-md">SENT</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center transition-transform group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-white/90 mb-2 drop-shadow-md"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                </path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span class="text-3xl font-black text-white tracking-widest drop-shadow-lg uppercase">
                                TAP
                            </span>
                        </div>
                    @endif
                </div>
            </button>
        </div>

        <!-- Warning Text -->
        <div class="text-center space-y-1 animate-fade-in" style="animation-delay: 0.2s;">
            <p class="text-white/80 font-bold uppercase tracking-widest text-sm">
                Single Tap to Alert Security
            </p>
            <p class="text-red-400/80 text-xs font-medium uppercase tracking-wider">
                False alarms may result in penalties
            </p>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div
                class="w-full max-w-md px-6 py-4 bg-green-500/20 border border-green-500/50 backdrop-blur-md text-green-400 rounded-xl font-bold shadow-lg text-center animate-scale-in">
                {{ session('message') }}
            </div>
        @endif

        <!-- Duty Roster Section -->
        <div class="w-full mt-8 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="flex items-end justify-between mb-6 px-2">
                <div>
                    <h2 class="text-2xl font-bold text-white">Duty Roster</h2>
                    <p class="text-white/50 text-sm">Today's Security Personnel</p>
                </div>
                <div class="px-3 py-1 bg-white/5 rounded-full text-xs text-white/70 border border-white/10">
                    {{ now()->format('d M Y') }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($this->duties as $duty)
                            <div
                                class="group bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-4 flex items-center gap-4 transition-all hover:scale-[1.02] hover:shadow-xl backdrop-blur-sm">

                                <!-- Avatar / Icon -->
                                <div
                                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center border border-white/10 group-hover:border-indigo-500/50 transition-colors">
                                    <span class="text-xl font-bold text-white/90">{{ substr($duty->guard_name, 0, 1) }}</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-bold text-white text-lg truncate pr-2">{{ $duty->guard_name }}</h3>
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                                        {{ $duty->shift === 'Morning' ? 'bg-orange-500/20 text-orange-400' :
                    ($duty->shift === 'Evening' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-blue-500/20 text-blue-400') }}">
                                            {{ $duty->shift }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-4 text-xs text-white/50">
                                        <div class="flex items-center gap-1.5 break-all">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="truncate max-w-[100px]">{{ $duty->location }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($duty->contact_number)
                                    <a href="tel:{{ $duty->contact_number }}"
                                        class="w-10 h-10 rounded-full bg-green-500/20 hover:bg-green-500/40 text-green-400 flex items-center justify-center border border-green-500/30 transition-all hover:scale-110 shadow-lg"
                                        title="Call Security">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                            </path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                @empty
                    <div
                        class="col-span-full py-8 text-center bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                        <p class="text-white/40 italic">No security personnel assigned for today.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out forwards;
            opacity: 0;
        }

        .animate-scale-in {
            animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-pulse-slow {
            animation: pulseSlow 3s infinite;
        }

        @keyframes pulseSlow {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .animate-bounce-slow {
            animation: bounceSlow 2s infinite;
        }

        @keyframes bounceSlow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }
    </style>
</div>