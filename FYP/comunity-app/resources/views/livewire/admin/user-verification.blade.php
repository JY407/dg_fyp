<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    public function with()
    {
        return [
            'pendingUsers' => User::where('status', 'pending')->latest()->get(),
        ];
    }

    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'approved';
        $user->save();
        $this->dispatch('user-verified', message: 'User approved successfully.');
    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'rejected';
        $user->save();
        $this->dispatch('user-verified', message: 'User rejected.');
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-500">
            Pending Verifications
        </h2>
    </div>

    @if($pendingUsers->isEmpty())
        <div
            class="bg-[rgba(255,255,255,0.03)] backdrop-blur-md rounded-2xl p-12 text-center border border-[rgba(255,255,255,0.05)]">
            <div class="text-gray-400 text-lg">No pending registration requests.</div>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($pendingUsers as $user)
                <div
                    class="glass-card p-6 flex items-center justify-between rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)]">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-white font-bold text-lg">{{ $user->name }}</div>
                            <div class="text-gray-400 text-sm">{{ $user->email }}</div>
                            <div class="text-xs text-indigo-400 mt-1">
                                Applied for: <span class="uppercase font-bold">{{ $user->user_type }}</span> •
                                Unit: {{ $user->unit_number ?? 'N/A' }}
                                @if($user->created_by)
                                    <span class="text-gray-500 ml-2">(by
                                        {{ \App\Models\User::find($user->created_by)->name ?? 'User' }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button wire:click="approve({{ $user->id }})"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-bold transition-colors">
                            Approve
                        </button>
                        <button wire:click="reject({{ $user->id }})"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-bold transition-colors">
                            Reject
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-action-message on="user-verified" />
</div>