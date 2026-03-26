<?php

use Livewire\Volt\Component;
use App\Models\CultureEvent;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin')] class extends Component {
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $isEditing = false;
    public $eventId = null;
    public $title = '';
    public $description = '';
    public $event_date = '';
    public $image = null;
    public $existing_image = null;

    public function with()
    {
        return [
            'events' => CultureEvent::orderBy('event_date', 'desc')->paginate(10),
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['eventId', 'title', 'description', 'event_date', 'image', 'existing_image']);
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $event = CultureEvent::findOrFail($id);
        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->event_date = $event->event_date->format('Y-m-d');
        $this->existing_image = $event->image_path;
        $this->image = null;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'event_date'  => 'required|date',
            'image'       => $this->isEditing ? 'nullable|image|max:10240' : 'nullable|image|max:10240',
        ]);

        $imagePath = $this->existing_image;

        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $imagePath = $this->image->store('culture-images', 'public');
        }

        CultureEvent::updateOrCreate(
            ['id' => $this->eventId],
            [
                'title'       => $this->title,
                'description' => $this->description,
                'event_date'  => $this->event_date,
                'image_path'  => $imagePath,
            ]
        );

        $this->closeModal();
        session()->flash('success', $this->isEditing ? 'Culture event updated successfully.' : 'Culture event added successfully.');
    }

    public function delete($id)
    {
        $event = CultureEvent::findOrFail($id);
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();
        session()->flash('success', 'Culture event deleted successfully.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Culture & History Management</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage cultural events and historical records for the community.</p>
        </div>
        <button wire:click="openCreateModal"
            class="bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-600/20 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Culture Event
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs w-24">Image</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Title & Description</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Event Date</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($events as $event)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="h-16 w-24 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 shadow-sm">
                                    @if ($event->image_path)
                                        <img src="{{ asset('storage/' . $event->image_path) }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900 dark:text-white text-base mb-1">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 max-w-sm truncate">{{ $event->description }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $event->event_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $event->event_date->diffForHumans() }}</div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEditModal({{ $event->id }})"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all border border-indigo-100 dark:border-indigo-800 hover:border-transparent"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $event->id }})"
                                        wire:confirm="Delete this culture event?"
                                        class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-600 p-2.5 rounded-lg transition-all border border-red-100 dark:border-red-800 hover:border-transparent"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No culture events yet</h3>
                                    <p class="text-sm text-gray-500">Add the first cultural event or historical record.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($events->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-lg my-8 border border-gray-100 dark:border-gray-700 relative">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c-1.2 5.4-6 8-6 8s4.8 2.6 6 8c1.2-5.4 6-8 6-8s-4.8-2.6-6-8z" />
                            </svg>
                        </div>
                        {{ $isEditing ? 'Edit Event' : 'Add Culture Event' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <input type="text" wire:model="title" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 p-3.5 outline-none transition-all" placeholder="e.g. Merdeka Day Celebration">
                        @error('title') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Event Date</label>
                        <input type="date" wire:model="event_date" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 p-3.5 outline-none transition-all">
                        @error('event_date') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea wire:model="description" rows="4" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 p-3.5 outline-none transition-all" placeholder="Describe the event..."></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Image <span class="text-xs font-normal text-gray-400">(Optional)</span></label>
                        @if ($existing_image && !$image)
                            <div class="mb-3 h-32 rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                                <img src="{{ asset('storage/' . $existing_image) }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-purple-400 dark:hover:border-purple-500 rounded-xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/30 hover:bg-purple-50/30 transition-all">
                                <div class="flex flex-col items-center py-4">
                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">Click to upload image</p>
                                </div>
                                <input wire:model="image" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                        @if ($image)
                            <div class="mt-3 h-32 rounded-xl border border-purple-200 dark:border-purple-800 overflow-hidden">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        @error('image') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="closeModal" class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors outline-none">Cancel</button>
                    <button wire:click="save" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg shadow-purple-600/30 transition-all duration-300 hover:-translate-y-0.5 outline-none flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Add Event' }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>