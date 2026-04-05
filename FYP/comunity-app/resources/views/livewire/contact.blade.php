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
            width:100%; padding:10px 14px; border-radius:12px;
            border:1px solid rgba(71,85,105,.5); background:rgba(15,23,42,.7);
            color:#e2e8f0; font-size:14px; outline:none; transition:all .2s;
        }
        .contact-input::placeholder { color:#475569; }
        .contact-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.2); }
        .contact-input:read-only { opacity:.6; cursor:not-allowed; }
        .info-card { background:rgba(30,41,59,.6); border:1px solid rgba(71,85,105,.3); border-radius:16px; padding:16px 18px; display:flex; align-items:center; gap:14px; transition:all .2s; }
        .info-card:hover { border-color:rgba(99,102,241,.3); background:rgba(30,41,59,.9); }
    </style>
    @endpush

    <div class="px-6 pt-8 pb-10 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-xl shadow-indigo-900/40"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
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
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => 'hello@lcare.com', 'color' => '#6366f1'],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => '+60 3-1234 5678', 'color' => '#10b981'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Address', 'value' => '123 Community Street, KL', 'color' => '#ef4444'],
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Office Hours', 'value' => 'Mon–Fri, 9am – 5pm', 'color' => '#f59e0b'],
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

            {{-- Form --}}
            <div class="lg:col-span-2 rounded-2xl border border-slate-700/50 overflow-hidden shadow-2xl" style="background:rgba(15,23,42,.8);">
                <div class="px-7 py-5 border-b border-slate-700/50" style="background:rgba(30,41,59,.5);">
                    <h2 class="text-base font-bold text-white">{{ __('app.contact_send') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('app.contact_subtitle') }}</p>
                </div>
                <div class="p-7">
                    @if(session()->has('success'))
                        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium text-emerald-300"
                            style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25);">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit="submitMessage" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('app.contact_name') }}</label>
                                <input wire:model="name" type="text" placeholder="Your full name"
                                    class="contact-input {{ auth()->check() ? 'readonly' : '' }}"
                                    {{ auth()->check() ? 'readonly' : '' }}>
                                @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('app.contact_email') }}</label>
                                <input wire:model="email" type="email" placeholder="your@email.com"
                                    class="contact-input {{ auth()->check() ? 'readonly' : '' }}"
                                    {{ auth()->check() ? 'readonly' : '' }}>
                                @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('app.contact_subject') }}</label>
                            <input wire:model="subject" type="text" placeholder="What is this about?" class="contact-input">
                            @error('subject') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">{{ __('app.contact_message') }}</label>
                            <textarea wire:model="message" rows="5" placeholder="Type your message here…" class="contact-input" style="resize:none;"></textarea>
                            @error('message') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit"
                            class="w-full py-3 rounded-xl font-bold text-sm text-white flex items-center justify-center gap-2 transition-all duration-200 hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#6366f1,#8b5cf6); box-shadow:0 8px 25px rgba(99,102,241,.35);">
                                <span wire:loading.remove wire:target="submitMessage">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
