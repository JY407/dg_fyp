<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ContactMessage;


new #[Layout('layouts.app')] class extends Component {
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';

    public function mount()
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submitMessage()
    {
        $this->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create([
            'user_id' => auth()->id() ?? null,
            'name'    => $this->name,
            'email'   => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        if (!auth()->check()) $this->reset(['name', 'email']);
        $this->reset(['subject', 'message']);
        session()->flash('success', 'Your message has been sent! We will get back to you soon.');
    }
}; ?>

<div class="min-h-screen" style="background:#0f172a;">
    @push('styles')
    <style>
        .contact-input {
            width:100%; padding:12px 16px; border-radius:8px;
            border:1px solid #d1d5db; background:#ffffff;
            color:#1f2937; font-size:14px; outline:none; transition:all .2s;
        }
        .contact-input::placeholder { color:#9ca3af; }
        .contact-input:focus { border-color:transparent; box-shadow:0 0 0 2px #a855f7; }
        .contact-input:read-only { opacity:.6; cursor:not-allowed; }
        
        .dark .contact-input {
            background:#374151; border-color:#4b5563; color:#ffffff;
        }
        .dark .contact-input::placeholder { color:#6b7280; }

        .info-card { background:rgba(30,41,59,.6); border:1px solid rgba(71,85,105,.3); border-radius:16px; padding:16px 18px; display:flex; align-items:center; gap:14px; transition:all .2s; }
        .info-card:hover { border-color:rgba(139,92,246,.3); background:rgba(30,41,59,.9); }
    </style>
    @endpush

    <div class="px-6 pt-8 pb-10 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-xl shadow-purple-900/40"
                style="background:linear-gradient(135deg,#7c3aed,#db2777);">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ __('app.contact_title') }}</h1>
            <p class="text-slate-400 text-sm mt-2">{{ __('app.contact_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Info Cards --}}
            <div class="space-y-3">
                @foreach([
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => 'hello@lcare.com', 'color' => '#a855f7'],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => '+60 3-1234 5678', 'color' => '#db2777'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Address', 'value' => '123 Community Street, KL', 'color' => '#ec4899'],
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Office Hours', 'value' => 'Mon–Fri, 9am – 5pm', 'color' => '#8b5cf6'],
                ] as $info)
                    <div class="info-card">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                            style="background:{{ $info['color'] }}18; border:1px solid {{ $info['color'] }}30;">
                            <svg class="w-5 h-5" style="color:{{ $info['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $info['label'] }}</p>
                            <p class="text-sm font-semibold text-slate-200 mt-0.5">{{ $info['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Form Card --}}
            <div class="lg:col-span-2 bg-gray-900 rounded-3xl shadow-2xl border border-white/10 overflow-hidden">
                {{-- Card Header --}}
                <div class="relative bg-gradient-to-r from-purple-700 via-pink-700 to-indigo-800 px-8 pt-7 pb-6 overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 border border-white/20 text-purple-200 mb-3 uppercase tracking-wider">
                            ✉️ Send a Message
                        </span>
                        <h2 class="text-xl font-black text-white">{{ __('app.contact_send') }}</h2>
                        <p class="text-sm text-purple-200/80 mt-1">{{ __('app.contact_subtitle') }}</p>
                    </div>
                </div>

                {{-- Form Body --}}
                <div class="p-7">
                    @if(session()->has('success'))
                        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold text-green-300 bg-green-950/40 border border-green-700/50">
                            <svg class="w-5 h-5 shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit="submitMessage" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">{{ __('app.contact_name') }} <span class="text-red-400">*</span></label>
                                <input wire:model="name" type="text" placeholder="Your full name"
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all {{ auth()->check() ? 'opacity-60 cursor-not-allowed' : '' }}"
                                    {{ auth()->check() ? 'readonly' : '' }}>
                                @error('name') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">{{ __('app.contact_email') }} <span class="text-red-400">*</span></label>
                                <input wire:model="email" type="email" placeholder="your@email.com"
                                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all {{ auth()->check() ? 'opacity-60 cursor-not-allowed' : '' }}"
                                    {{ auth()->check() ? 'readonly' : '' }}>
                                @error('email') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">{{ __('app.contact_subject') }} <span class="text-red-400">*</span></label>
                            <input wire:model="subject" type="text" placeholder="What is this about?"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            @error('subject') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">{{ __('app.contact_message') }} <span class="text-red-400">*</span></label>
                            <textarea wire:model="message" rows="5" placeholder="Type your message here…"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none"></textarea>
                            @error('message') <span class="text-red-400 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-1">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-purple-900/40 transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submitMessage" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    {{ __('app.contact_send') }}
                                </span>
                                <span wire:loading wire:target="submitMessage" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('app.loading') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
