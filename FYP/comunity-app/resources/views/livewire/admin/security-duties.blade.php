<?php

use Livewire\Volt\Component;
use App\Models\SecurityDuty;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    public $date;
    public $guard_name;
    public $shift = 'Morning';
    public $location;

    public function with()
    {
        return [
            'duties' => SecurityDuty::orderBy('date', 'desc')
                ->orderBy('shift')
                ->get(),
        ];
    }

    public function addDuty()
    {
        $this->validate([
            'date' => 'required|date',
            'guard_name' => 'required|string|max:255',
            'shift' => 'required|string',
            'location' => 'required|string|max:255',
        ]);

        SecurityDuty::create([
            'date' => $this->date,
            'guard_name' => $this->guard_name,
            'shift' => $this->shift,
            'location' => $this->location,
        ]);

        $this->reset(['date', 'guard_name', 'shift', 'location']);
        $this->dispatch('duty-added');
    }

    public function deleteDuty($id)
    {
        SecurityDuty::find($id)->delete();
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-500">
            Security Duty Roster
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add New Duty Form -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)]">
                <h3 class="text-xl font-bold text-white mb-4">Add New Duty</h3>
                <form wire:submit="addDuty" class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Date</label>
                        <input wire:model="date" type="date"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                        @error('date') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Guard Name</label>
                        <input wire:model="guard_name" type="text" placeholder="Officer Name"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                        @error('guard_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Shift</label>
                        <select wire:model="shift"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                            <option value="Morning">Morning (08:00 - 16:00)</option>
                            <option value="Evening">Evening (16:00 - 00:00)</option>
                            <option value="Night">Night (00:00 - 08:00)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Location / Post</label>
                        <input wire:model="location" type="text" placeholder="e.g. Main Gate"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                        @error('location') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg transition-colors">
                        Assign Duty
                    </button>

                    <x-action-message on="duty-added" class="text-green-400 text-center text-sm" />
                </form>
            </div>
        </div>

        <!-- Duty List -->
        <div class="lg:col-span-2">
            <div class="space-y-4">
                @forelse($duties as $duty)
                    <div
                        class="glass-card p-4 rounded-xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)] flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-lg bg-gray-800 flex flex-col items-center justify-center border border-gray-700">
                                <span
                                    class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($duty->date)->format('M') }}</span>
                                <span
                                    class="text-lg font-bold text-white">{{ \Carbon\Carbon::parse($duty->date)->format('d') }}</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg">{{ $duty->guard_name }}</h4>
                                <div class="text-sm text-gray-400 flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 rounded text-xs font-bold {{ $duty->shift === 'Night' ? 'bg-purple-500/10 text-purple-400' : 'bg-blue-500/10 text-blue-400' }}">
                                        {{ $duty->shift }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $duty->location }}</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="deleteDuty({{ $duty->id }})"
                            class="text-gray-500 hover:text-red-500 transition-colors" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                        </button>
                    </div>
                @empty
                    <div
                        class="text-center py-12 text-gray-500 bg-[rgba(255,255,255,0.02)] rounded-xl border border-[rgba(255,255,255,0.05)]">
                        No duties scheduled.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>