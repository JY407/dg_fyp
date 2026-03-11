<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ContactMessage;

new #[Layout('layouts.admin')] class extends Component {
    public function with()
    {
        return [
            'messages' => ContactMessage::latest()->get()
        ];
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::find($id);
        if ($message) {
            $message->update(['is_read' => true]);
        }
    }

    public function markAsUnread($id)
    {
        $message = ContactMessage::find($id);
        if ($message) {
            $message->update(['is_read' => false]);
        }
    }

    public function deleteMessage($id)
    {
        $message = ContactMessage::find($id);
        if ($message) {
            $message->delete();
        }
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Contact Messages</h1>
            <p class="text-gray-500 text-sm mt-1">Manage inquiries and feedback from the community.</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-indigo-50 px-4 py-2 rounded-xl text-center border border-indigo-100">
                <span class="block text-2xl font-bold text-indigo-600">{{ $messages->where('is_read', false)->count() }}</span>
                <span class="text-xs text-indigo-600 font-medium uppercase tracking-wider text-nowrap">Unread</span>
            </div>
            <div class="bg-gray-50 px-4 py-2 rounded-xl text-center border border-gray-100">
                <span class="block text-2xl font-bold text-gray-600">{{ $messages->count() }}</span>
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider text-nowrap">Total</span>
            </div>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($messages->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-4"><path d="M21.2 8.4c.5.38.8.97.8 1.6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10a2 2 0 0 1 .8-1.6l8-6a2 2 0 0 1 2.4 0l8 6Z"/><path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10"/></svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Messages Yet</h3>
                <p>When users send a message through the Contact Us page, it will appear here.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($messages as $msg)
                    <li class="p-6 transition hover:bg-gray-50 {{ $msg->is_read ? '' : 'bg-indigo-50/30' }}">
                        <div class="flex items-start justify-between gap-6">
                            
                            <!-- Sender Info -->
                            <div class="flex gap-4 shrink-0 w-64 border-r border-gray-100">
                                @if($msg->user && $msg->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $msg->user->profile_photo_path) }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0">
                                        {{ str($msg->name)->substr(0, 1)->upper() }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 truncate max-w-[150px]">{{ $msg->name }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5 max-w-[150px]">{{ $msg->email }}</p>
                                    <div class="mt-2 text-[10px] font-medium px-2 py-0.5 rounded-full inline-block {{ $msg->user_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $msg->user_id ? 'Registered User' : 'Guest' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h4 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                        @if(!$msg->is_read)
                                            <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full inline-block" title="Unread"></span>
                                        @endif
                                        {{ $msg->subject }}
                                    </h4>
                                    <span class="text-xs text-gray-400 {{ !$msg->is_read ? 'font-semibold text-indigo-600' : '' }} whitespace-nowrap ml-4">
                                        {{ $msg->created_at->format('M d, Y h:i A') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 whitespace-pre-wrap mt-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    {{ $msg->message }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2 shrink-0">
                                @if(!$msg->is_read)
                                    <button wire:click="markAsRead({{ $msg->id }})" class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition tooltip shrink-0" title="Mark as Read">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                                    </button>
                                @else
                                    <button wire:click="markAsUnread({{ $msg->id }})" class="p-2 text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-lg transition tooltip shrink-0" title="Mark as Unread">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </button>
                                @endif
                                
                                <button wire:click="deleteMessage({{ $msg->id }})" wire:confirm="Are you sure you want to delete this message?" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition tooltip shrink-0" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                </button>
                            </div>

                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
