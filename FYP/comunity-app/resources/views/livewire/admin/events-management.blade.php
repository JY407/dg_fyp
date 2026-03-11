<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $currentTab = 'pending'; // pending, all

    public function with()
    {
        $query = Event::with(['creator', 'participants'])->orderBy('created_at', 'desc');
        
        if ($this->currentTab === 'pending') {
            $query->where('status', 'pending');
        }

        return [
            'events' => $query->paginate(10)
        ];
    }

    public function setTab($tab)
    {
        $this->currentTab = $tab;
        $this->resetPage();
    }

    public function approveEvent(Event $event)
    {
        $event->update(['status' => 'approved']);
        session()->flash('success', "Event '{$event->title}' has been approved successfully.");
    }

    public function rejectEvent(Event $event)
    {
        $event->update(['status' => 'rejected']);
        session()->flash('success', "Event '{$event->title}' has been rejected.");
    }
    
    public function deleteEvent(Event $event)
    {
         $event->delete();
         session()->flash('success', "Event deleted successfully.");
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-500">
                Community Events Management
            </h2>
            <p class="text-gray-400 mt-2">Verify and manage events submitted by community members.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="flex space-x-2 mb-6 border-b border-[rgba(255,255,255,0.1)] pb-px">
        <button wire:click="setTab('pending')" class="px-6 py-3 font-medium text-sm rounded-t-lg transition-colors {{ $currentTab === 'pending' ? 'bg-[rgba(255,255,255,0.05)] text-indigo-400 border-t border-l border-r border-[rgba(255,255,255,0.1)]' : 'text-gray-400 hover:text-white hover:bg-[rgba(255,255,255,0.02)]' }}">
            <div class="flex items-center gap-2">
                Pending Verification
                @if($currentTab === 'pending' && $events->total() > 0)
                    <span class="bg-indigo-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $events->total() }}</span>
                @endif
            </div>
        </button>
        <button wire:click="setTab('all')" class="px-6 py-3 font-medium text-sm rounded-t-lg transition-colors {{ $currentTab === 'all' ? 'bg-[rgba(255,255,255,0.05)] text-indigo-400 border-t border-l border-r border-[rgba(255,255,255,0.1)]' : 'text-gray-400 hover:text-white hover:bg-[rgba(255,255,255,0.02)]' }}">
            All Events
        </button>
    </div>

    <!-- Events List -->
    <div class="glass-card rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-[rgba(255,255,255,0.02)] border-b border-[rgba(255,255,255,0.05)] text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Event Details</th>
                        <th class="px-6 py-4 font-semibold">Date & Time</th>
                        <th class="px-6 py-4 font-semibold">Created By</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                    @forelse($events as $event)
                        <tr class="hover:bg-[rgba(255,255,255,0.02)] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if($event->image_path)
                                        <div class="h-12 w-16 rounded overflow-hidden bg-gray-800 flex-shrink-0">
                                            <img src="{{ asset('storage/' . $event->image_path) }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="h-12 w-16 rounded bg-indigo-500/10 flex items-center justify-center text-indigo-400 flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-white text-base">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $event->location }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-300">{{ $event->event_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-indigo-400 font-medium">{{ $event->creator->name }}</div>
                                <div class="text-xs text-gray-500">{{ $event->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Approved
                                    </span>
                                @elseif($event->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($event->status === 'pending')
                                        <button wire:click="approveEvent({{ $event->id }})" class="p-2 text-green-400 hover:bg-green-500/10 rounded-lg transition-colors" title="Approve">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                        </button>
                                        <button wire:click="rejectEvent({{ $event->id }})" class="p-2 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Reject">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </button>
                                    @else
                                        @if($event->status === 'rejected')
                                            <button wire:click="approveEvent({{ $event->id }})" class="p-2 text-gray-400 hover:text-green-400 hover:bg-green-500/10 rounded-lg transition-colors" title="Approve">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>
                                            </button>
                                        @endif
                                        <button wire:confirm="Are you sure you want to delete this event?" wire:click="deleteEvent({{ $event->id }})" class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-[rgba(255,255,255,0.02)] rounded-full flex items-center justify-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-400">No events found.</p>
                                    <p class="text-sm mt-1">There are currently no events matching this status.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="px-6 py-4 border-t border-[rgba(255,255,255,0.05)]">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
