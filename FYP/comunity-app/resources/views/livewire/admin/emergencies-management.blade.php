<?php

use Livewire\Volt\Component;
use App\Models\EmergencyAlert;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $filterStatus = 'all';

    public function with()
    {
        $query = EmergencyAlert::with('user')->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return [
            'emergencies' => $query->paginate(15),
            'activeCount' => EmergencyAlert::where('status', 'active')->count(),
        ];
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function resolve($id)
    {
        EmergencyAlert::findOrFail($id)->update(['status' => 'resolved']);
        session()->flash('success', 'Emergency alert marked as resolved.');
    }

    public function delete($id)
    {
        EmergencyAlert::findOrFail($id)->delete();
        session()->flash('success', 'Emergency alert record deleted.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                Emergencies Management
                @if($activeCount > 0)
                    <span class="bg-red-500 text-white text-sm font-bold px-3 py-0.5 rounded-full animate-pulse">{{ $activeCount }} Active</span>
                @endif
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Monitor and respond to emergency alerts from residents.</p>
        </div>

        <div class="flex gap-2 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
            <button wire:click="setFilter('all')" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterStatus === 'all' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                All
            </button>
            <button wire:click="setFilter('active')" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterStatus === 'active' ? 'bg-white dark:bg-gray-700 text-red-600 dark:text-red-400 shadow' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                Active
                @if($activeCount > 0)
                    <span class="ml-1.5 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $activeCount }}</span>
                @endif
            </button>
            <button wire:click="setFilter('resolved')" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterStatus === 'resolved' ? 'bg-white dark:bg-gray-700 text-green-600 dark:text-green-400 shadow' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                Resolved
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
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Reported At</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Resident</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Type & Message</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Status</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($emergencies as $emergency)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group {{ $emergency->status === 'active' ? 'bg-red-50/30 dark:bg-red-900/10' : '' }}">
                            <td class="px-8 py-5 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $emergency->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $emergency->created_at->format('h:i A') }} &bull; {{ $emergency->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900 dark:text-white">{{ optional($emergency->user)->name ?? '—' }}</div>
                                @if(optional($emergency->user)->unit_number)
                                    <div class="text-xs text-gray-400">Unit {{ $emergency->user->unit_number }}</div>
                                @endif
                            </td>
                            <td class="px-8 py-5 max-w-xs">
                                <div class="font-bold text-gray-900 dark:text-white uppercase text-sm">{{ str_replace('_', ' ', $emergency->type) }}</div>
                                @if($emergency->message)
                                    <div class="text-xs text-gray-400 mt-0.5 truncate max-w-[220px]">{{ $emergency->message }}</div>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if ($emergency->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        Resolved
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    @if ($emergency->status === 'active')
                                        <button wire:click="resolve({{ $emergency->id }})"
                                            class="text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 hover:bg-green-600 hover:text-white dark:hover:bg-green-500 border border-green-200 dark:border-green-800 hover:border-transparent px-4 py-2 rounded-lg font-semibold text-xs transition-all"
                                            title="Mark Resolved">
                                            ✓ Resolve
                                        </button>
                                    @endif
                                    <button wire:click="delete({{ $emergency->id }})"
                                        wire:confirm="Permanently delete this emergency record?"
                                        class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-600 p-2.5 rounded-lg transition-all duration-200 border border-red-100 dark:border-red-800 hover:border-transparent"
                                        title="Delete Record">
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
                                    <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600 text-green-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">
                                        {{ $filterStatus === 'active' ? 'No active emergencies' : 'No emergency records' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">Everything looks safe and quiet!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($emergencies->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $emergencies->links() }}
            </div>
        @endif
    </div>
</div>
