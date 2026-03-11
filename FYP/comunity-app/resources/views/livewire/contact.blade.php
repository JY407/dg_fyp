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
            // Leave subject and message blank for the user to fill
        }
    }

    public function submitMessage()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create([
            'user_id' => auth()->id() ?? null,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        if (!auth()->check()) {
            $this->reset(['name', 'email']);
        }
        $this->reset(['subject', 'message']);

        session()->flash('success', 'Your message has been sent successfully. We will get back to you soon!');
    }
}; ?>

<section style="padding: 120px 0 80px; margin-top: 70px; min-height: 100vh;">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 style="text-align: center;">Get In Touch</h1>
            <p class="text-secondary" style="text-align: center; margin-bottom: 3rem;">
                Have questions or suggestions? We'd love to hear from you!
            </p>

            @if (session()->has('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-xl text-center mb-8 font-medium animate-[pulse_2s_ease-in-out_infinite]">
                    {{ session('success') }}
                </div>
            @endif

            <div class="glass-card-lg">
                <form wire:submit="submitMessage">
                    <div class="form-group mb-6">
                        <label class="form-label text-xs tracking-wider text-gray-400 font-semibold mb-2 block uppercase">Name</label>
                        <input wire:model="name" type="text" class="form-input" placeholder="Your name" required {{ auth()->check() ? 'readonly' : '' }}>
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label class="form-label text-xs tracking-wider text-gray-400 font-semibold mb-2 block uppercase">Email</label>
                        <input wire:model="email" type="email" class="form-input" placeholder="your@email.com" required {{ auth()->check() ? 'readonly' : '' }}>
                        @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label class="form-label text-xs tracking-wider text-gray-400 font-semibold mb-2 block uppercase">Subject</label>
                        <input wire:model="subject" type="text" class="form-input" placeholder="What is this about?" required>
                        @error('subject') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-8">
                        <label class="form-label text-xs tracking-wider text-gray-400 font-semibold mb-2 block uppercase">Message</label>
                        <textarea wire:model="message" class="form-textarea" placeholder="Your message here..." required></textarea>
                        @error('message') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full text-center py-4 rounded-xl text-lg font-bold">
                        <svg wire:loading wire:target="submitMessage" class="animate-spin h-5 w-5 mr-3 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Send Message
                    </button>
                </form>
            </div>

            <div class="grid grid-3" style="margin-top: 3rem; text-align: center; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem;">
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📧</div>
                    <div class="text-gray-400" style="font-size: 0.875rem;">Email</div>
                    <div class="text-indigo-400">hello@community.com</div>
                </div>
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📞</div>
                    <div class="text-gray-400" style="font-size: 0.875rem;">Phone</div>
                    <div class="text-indigo-400">+1 (555) 123-4567</div>
                </div>
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📍</div>
                    <div class="text-gray-400" style="font-size: 0.875rem;">Address</div>
                    <div class="text-indigo-400">123 Community St</div>
                </div>
            </div>
        </div>
    </div>
</section>
