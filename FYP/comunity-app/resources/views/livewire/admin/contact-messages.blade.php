<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ContactMessage;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $filterTab = 'all'; // all, unread

    public function with()
    {
        $query = ContactMessage::latest();
        if ($this->filterTab === 'unread') {
            $query->where('is_read', false);
        }
        return [
            'messages'   => $query->paginate(10),
            'unreadCount' => ContactMessage::where('is_read', false)->count(),
        ];
    }

    public function setFilter($tab)
    {
        $this->filterTab = $tab;
        $this->resetPage();
    }

    public function markAsRead($id)
    {
        ContactMessage::findOrFail($id)->update(['is_read' => true]);
    }

    public function markAsUnread($id)
    {
        ContactMessage::findOrFail($id)->update(['is_read' => false]);
    }

    public function deleteMessage($id)
    {
        ContactMessage::findOrFail($id)->delete();
        session()->flash('success', 'Message deleted.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                Contact Messages
                @if($unreadCount > 0)
                    <span class="bg-indigo-600 text-white text-sm font-bold px-3 py-0.5 rounded-full">{{ $unreadCount }} Unread</span>
                @endif
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage inquiries and feedback from the community.</p>
        </div>
        <div class="flex gap-2 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
            <button wire:click="setFilter('all')" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterTab === 'all' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                All
            </button>
            <button wire:click="setFilter('unread')" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterTab === 'unread' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                Unread
                @if($unreadCount > 0)
                    <span class="ml-1.5 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                @endif
            </button>
        </div>
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
            <table class="w-full text-left text-sm">
                <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Sender</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Subject & Message</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Received</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Status</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($messages as $msg)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group {{ !$msg->is_read ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : '' }}">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    @if($msg->user && $msg->user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $msg->user->profile_photo_path) }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 font-bold flex items-center justify-center shrink-0 text-sm">
                                            {{ strtoupper(substr($msg->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white {{ !$msg->is_read ? 'text-indigo-800 dark:text-indigo-300' : '' }}">{{ $msg->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $msg->email }}</div>
                                        <span class="mt-1 inline-block text-[10px] font-medium px-2 py-0.5 rounded-full {{ $msg->user_id ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                            {{ $msg->user_id ? 'Registered' : 'Guest' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 max-w-md">
                                <div class="font-bold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                                    @if(!$msg->is_read)
                                        <span class="w-2 h-2 bg-indigo-600 rounded-full shrink-0"></span>
                                    @endif
                                    {{ $msg->subject }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-wrap line-clamp-2">{{ $msg->message }}</div>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $msg->created_at->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-400">{{ $msg->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                @if (!$msg->is_read)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse"></span>
                                        Unread
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        Read
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    @if (!$msg->is_read)
                                        <button wire:click="markAsRead({{ $msg->id }})"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all border border-indigo-100 dark:border-indigo-800 hover:border-transparent"
                                            title="Mark as Read">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="markAsUnread({{ $msg->id }})"
                                            class="text-gray-400 hover:text-white hover:bg-gray-500 bg-gray-100 dark:bg-gray-700 p-2.5 rounded-lg transition-all border border-gray-200 dark:border-gray-600 hover:border-transparent"
                                            title="Mark as Unread">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="deleteMessage({{ $msg->id }})"
                                        wire:confirm="Delete this message?"
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
                            <td colspan="5" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">
                                        {{ $filterTab === 'unread' ? 'No unread messages' : 'No messages yet' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">Messages from the Contact page will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($messages->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
