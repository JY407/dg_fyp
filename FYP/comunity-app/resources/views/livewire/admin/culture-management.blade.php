<?php

use Livewire\Volt\Component;
use App\Models\CultureEvent;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

new #[Layout('layouts.admin')] class extends Component {
    use WithFileUploads;

    public $title;
    public $description;
    public $event_date;
    public $image;

    public function with()
    {
        return [
            'events' => CultureEvent::orderBy('event_date', 'desc')->get()
        ];
    }

    public function addEvent()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('culture-images', 'public');
        }

        CultureEvent::create([
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'image_path' => $imagePath,
        ]);

        $this->reset(['title', 'description', 'event_date', 'image']);
        $this->dispatch('event-added');
    }

    public function deleteEvent($id)
    {
        CultureEvent::find($id)->delete();
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-500">
            Culture & History Management
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Event Form -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)]">
                <h3 class="text-xl font-bold text-white mb-4">Add New Event/History</h3>
                <form wire:submit="addEvent" class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Title</label>
                        <input wire:model="title" type="text" placeholder="e.g. Merdeka Day"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500">
                        @error('title') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Date</label>
                        <input wire:model="event_date" type="date"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500 color-scheme-dark">
                        @error('event_date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Image</label>
                        <input wire:model="image" type="file" accept="image/*"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="mt-2 rounded-lg max-h-32 object-cover">
                        @endif
                        @error('image') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Description</label>
                        <textarea wire:model="description" rows="4" placeholder="Description of the event or history..."
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500"></textarea>
                        @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded-lg transition-colors">
                        Add Event
                    </button>
                    <x-action-message on="event-added" class="text-green-400 text-center text-sm" />
                </form>
            </div>
        </div>

        <!-- Events List -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 gap-4">
                @forelse($events as $event)
                    <div
                        class="glass-card p-5 rounded-xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)] relative group hover:bg-[rgba(255,255,255,0.05)] transition-colors flex gap-4 items-start">

                        @if($event->image_path)
                            <div class="w-32 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-800">
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @else
                            <div
                                class="w-32 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-purple-500/10 flex items-center justify-center text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-white font-bold text-lg">{{ $event->title }}</h4>
                                    <p class="text-sm text-purple-300 mb-2">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                    </p>
                                </div>
                                <button wire:click="deleteEvent({{ $event->id }})"
                                    class="text-gray-500 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-gray-400 text-sm line-clamp-2">{{ $event->description }}</p>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full text-center py-12 text-gray-500 bg-[rgba(255,255,255,0.02)] rounded-xl border border-[rgba(255,255,255,0.05)]">
                        No culture events or history added yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>