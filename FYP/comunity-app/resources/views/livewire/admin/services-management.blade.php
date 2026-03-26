<?php

use Livewire\Volt\Component;
use App\Models\CommunityService;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $showModal = false;
    public $isEditing = false;
    public $serviceId = null;

    public $service_name = '';
    public $provider_name = '';
    public $frequency = 'Weekly';
    public $day_of_week = '';
    public $time_slot = '';
    public $description = '';
    public $contact_number = '';

    public function with()
    {
        return [
            'services' => CommunityService::orderBy('created_at', 'desc')->paginate(10),
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['serviceId', 'service_name', 'provider_name', 'day_of_week', 'time_slot', 'description', 'contact_number']);
        $this->frequency = 'Weekly';
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $service = CommunityService::findOrFail($id);
        $this->serviceId = $service->id;
        $this->service_name = $service->service_name;
        $this->provider_name = $service->provider_name;
        $this->frequency = $service->frequency;
        $this->day_of_week = $service->day_of_week ?? '';
        $this->time_slot = $service->time_slot ?? '';
        $this->description = $service->description ?? '';
        $this->contact_number = $service->contact_number ?? '';
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
            'service_name'   => 'required|string|max:255',
            'provider_name'  => 'required|string|max:255',
            'frequency'      => 'required|string',
            'day_of_week'    => 'nullable|string',
            'time_slot'      => 'nullable|string',
            'description'    => 'nullable|string',
            'contact_number' => 'nullable|string',
        ]);

        CommunityService::updateOrCreate(
            ['id' => $this->serviceId],
            [
                'service_name'   => $this->service_name,
                'provider_name'  => $this->provider_name,
                'frequency'      => $this->frequency,
                'day_of_week'    => $this->day_of_week,
                'time_slot'      => $this->time_slot,
                'description'    => $this->description,
                'contact_number' => $this->contact_number,
            ]
        );

        $this->closeModal();
        session()->flash('success', $this->isEditing ? 'Service updated successfully.' : 'Service added successfully.');
    }

    public function delete($id)
    {
        CommunityService::findOrFail($id)->delete();
        session()->flash('success', 'Service deleted successfully.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Community Services</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage scheduled community services for residents.</p>
        </div>
        <button wire:click="openCreateModal"
            class="bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Service
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
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Service</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Provider</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Schedule</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Contact</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($services as $service)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900 dark:text-white text-base">{{ $service->service_name }}</div>
                                @if($service->description)
                                    <div class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">{{ $service->description }}</div>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-gray-700 dark:text-gray-300 font-medium">{{ $service->provider_name }}</td>
                            <td class="px-8 py-5">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                        {{ $service->frequency }}
                                    </span>
                                    @if($service->day_of_week)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-100 dark:border-purple-800">
                                            {{ $service->day_of_week }}
                                        </span>
                                    @endif
                                    @if($service->time_slot)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-100 dark:border-green-800">
                                            {{ $service->time_slot }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-500 dark:text-gray-400">{{ $service->contact_number ?? '—' }}</td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEditModal({{ $service->id }})"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all border border-indigo-100 dark:border-indigo-800 hover:border-transparent"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $service->id }})"
                                        wire:confirm="Delete this community service?"
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No services configured</h3>
                                    <p class="text-sm text-gray-500">Add community services for residents to view.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($services->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $services->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-lg my-8 border border-gray-100 dark:border-gray-700">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                            </svg>
                        </div>
                        {{ $isEditing ? 'Edit Service' : 'Add New Service' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Service Type</label>
                        <input type="text" wire:model="service_name" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all" placeholder="e.g. Garbage Collection">
                        @error('service_name') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Provider Name</label>
                        <input type="text" wire:model="provider_name" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all" placeholder="e.g. City Services">
                        @error('provider_name') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Frequency</label>
                            <select wire:model="frequency" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all">
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Ad-hoc">Ad-hoc</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Day of Week</label>
                            <select wire:model="day_of_week" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all">
                                <option value="">Select Day</option>
                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Time Slot <span class="text-xs font-normal text-gray-400">(Optional)</span></label>
                        <input type="text" wire:model="time_slot" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all" placeholder="e.g. 08:00 AM - 10:00 AM">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Contact Number <span class="text-xs font-normal text-gray-400">(Optional)</span></label>
                        <input type="text" wire:model="contact_number" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 outline-none transition-all" placeholder="e.g. 03-1234 5678">
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="closeModal" class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors outline-none">Cancel</button>
                    <button wire:click="save" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-300 hover:-translate-y-0.5 outline-none flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Add Service' }}</span>
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