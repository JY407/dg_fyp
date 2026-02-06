<?php

use Livewire\Volt\Component;
use App\Models\CommunityService;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component {
    public $service_name;
    public $provider_name;
    public $frequency = 'Weekly';
    public $day_of_week;
    public $time_slot;
    public $description;
    public $contact_number;

    public function with()
    {
        return [
            'services' => CommunityService::orderBy('created_at', 'desc')->get()
        ];
    }

    public function addService()
    {
        $this->validate([
            'service_name' => 'required|string|max:255',
            'provider_name' => 'required|string|max:255',
            'frequency' => 'required|string',
            'day_of_week' => 'nullable|string',
            'time_slot' => 'nullable|string',
            'contact_number' => 'nullable|string',
        ]);

        CommunityService::create([
            'service_name' => $this->service_name,
            'provider_name' => $this->provider_name,
            'frequency' => $this->frequency,
            'day_of_week' => $this->day_of_week,
            'time_slot' => $this->time_slot,
            'description' => $this->description,
            'contact_number' => $this->contact_number,
        ]);

        $this->reset(['service_name', 'provider_name', 'frequency', 'day_of_week', 'time_slot', 'description', 'contact_number']);
        $this->dispatch('service-added');
    }

    public function deleteService($id)
    {
        CommunityService::find($id)->delete();
    }
}; ?>

<div class="container mx-auto py-12 px-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-500">
            Community Services Management
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Service Form -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 rounded-2xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)]">
                <h3 class="text-xl font-bold text-white mb-4">Add New Service</h3>
                <form wire:submit="addService" class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Service Type</label>
                        <input wire:model="service_name" type="text" placeholder="e.g. Garbage Collection"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                        @error('service_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Provider Name</label>
                        <input wire:model="provider_name" type="text" placeholder="e.g. City Services"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                        @error('provider_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-400 text-sm mb-1">Frequency</label>
                            <select wire:model="frequency"
                                class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Ad-hoc">Ad-hoc</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-sm mb-1">Day</label>
                            <select wire:model="day_of_week"
                                class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Time Slot</label>
                        <input wire:model="time_slot" type="text" placeholder="e.g. 08:00 AM - 10:00 AM"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Contact Number</label>
                        <input wire:model="contact_number" type="text" placeholder="Optional"
                            class="w-full bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-lg px-3 py-2 text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg transition-colors">
                        Add Service
                    </button>
                    <x-action-message on="service-added" class="text-green-400 text-center text-sm" />
                </form>
            </div>
        </div>

        <!-- Service List -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($services as $service)
                    <div
                        class="glass-card p-5 rounded-xl border border-[rgba(255,255,255,0.05)] bg-[rgba(255,255,255,0.02)] relative group hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <button wire:click="deleteService({{ $service->id }})"
                            class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                        </button>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg">{{ $service->service_name }}</h4>
                                <p class="text-sm text-gray-400">{{ $service->provider_name }}</p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        class="px-2 py-0.5 rounded text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        {{ $service->frequency }}
                                    </span>
                                    @if($service->day_of_week)
                                        <span
                                            class="px-2 py-0.5 rounded text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                            {{ $service->day_of_week }}
                                        </span>
                                    @endif
                                    @if($service->time_slot)
                                        <span
                                            class="px-2 py-0.5 rounded text-xs font-bold bg-green-500/10 text-green-400 border border-green-500/20">
                                            {{ $service->time_slot }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full text-center py-12 text-gray-500 bg-[rgba(255,255,255,0.02)] rounded-xl border border-[rgba(255,255,255,0.05)]">
                        No services configured yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>