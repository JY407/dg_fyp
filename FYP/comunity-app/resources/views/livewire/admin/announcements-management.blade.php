<?php

use Livewire\Volt\Component;
use App\Models\Announcement;
use App\Models\UserNotification;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $showModal = false;
    public $isEditing = false;
    public $announcementId = null;
    public $title = '';
    public $content = '';
    public $published_at = '';

    public function with()
    {
        return [
            'announcements' => Announcement::latest('published_at')->paginate(10),
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['announcementId', 'title', 'content', 'published_at']);
        $this->published_at = now()->format('Y-m-d\TH:i');
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->announcementId = $announcement->id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->published_at = $announcement->published_at
            ? $announcement->published_at->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');
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
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'published_at' => 'required|date',
        ]);

        Announcement::updateOrCreate(
            ['id' => $this->announcementId],
            [
                'title'        => $this->title,
                'content'      => $this->content,
                'published_at' => $this->published_at,
            ]
        );

        // Push a real notification to all active residents
        $action = $this->isEditing ? 'updated' : 'posted';
        $snippet = \Illuminate\Support\Str::limit($this->content, 80);
        UserNotification::pushToAll(
            'announcement',
            '📢 ' . $this->title,
            "Admin has {$action} an announcement: {$snippet}"
        );

        $this->closeModal();
        session()->flash('success', $this->isEditing ? 'Announcement updated successfully.' : 'Announcement created successfully.');
    }

    public function delete($id)
    {
        Announcement::findOrFail($id)->delete();
        session()->flash('success', 'Announcement deleted successfully.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Announcements</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Create and manage community-wide announcements.</p>
        </div>
        <button wire:click="openCreateModal"
            class="bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Announcement
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
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Title</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Content</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Published At</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($announcements as $announcement)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900 dark:text-white text-base">{{ $announcement->title }}</div>
                            </td>
                            <td class="px-8 py-5 text-gray-500 dark:text-gray-400 max-w-xs">
                                <div class="truncate max-w-sm">{{ $announcement->content }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-gray-900 dark:text-white font-medium">
                                    {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $announcement->published_at ? $announcement->published_at->format('h:i A') : '' }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEditModal({{ $announcement->id }})"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 dark:hover:bg-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all duration-200 border border-indigo-100 dark:border-indigo-800 hover:border-transparent"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $announcement->id }})"
                                        wire:confirm="Are you sure you want to delete this announcement?"
                                        class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-600 p-2.5 rounded-lg transition-all duration-200 border border-red-100 dark:border-red-800 hover:border-transparent"
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No announcements yet</h3>
                                    <p class="text-sm text-gray-500">Create one to notify your community.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($announcements->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-xl my-8 border border-gray-100 dark:border-gray-700 relative">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        {{ $isEditing ? 'Edit Announcement' : 'New Announcement' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <input type="text" wire:model="title"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none"
                            placeholder="Announcement title">
                        @error('title') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Content</label>
                        <textarea wire:model="content" rows="6"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none"
                            placeholder="Write the announcement content here..."></textarea>
                        @error('content') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Publish Date & Time</label>
                        <input type="datetime-local" wire:model="published_at"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none">
                        @error('published_at') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal"
                        class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors outline-none">
                        Cancel
                    </button>
                    <button wire:click="save"
                        class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-300 transform hover:-translate-y-0.5 outline-none flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Create Announcement' }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
