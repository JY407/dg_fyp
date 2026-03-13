<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin')] class extends Component {
    use WithFileUploads;

    public $activeTab = 'facilities'; // 'facilities' or 'bookings'

    // Facility Form Fields
    public $facility_id;
    public $name;
    public $capacity;
    public $type;
    public $image;
    public $existing_image;
    public $showFacilityModal = false;
    public $isEditing = false;

    // Listeners
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function with()
    {
        return [
            'facilities' => Facility::all(),
            'pendingBookings' => FacilityBooking::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get(),
            'historyBookings' => FacilityBooking::with('user')->whereIn('status', ['approved', 'rejected'])->orderBy('updated_at', 'desc')->get(),
        ];
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // --- Facility CRUD ---
    public function openFacilityModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['facility_id', 'name', 'capacity', 'type', 'image', 'existing_image']);
        $this->isEditing = false;

        if ($id) {
            $facility = Facility::findOrFail($id);
            $this->facility_id = $facility->id;
            $this->name = $facility->name;
            $this->capacity = $facility->capacity;
            $this->type = $facility->type;
            $this->existing_image = $facility->image_path;
            $this->isEditing = true;
        }

        $this->showFacilityModal = true;
    }

    public function closeFacilityModal()
    {
        $this->showFacilityModal = false;
    }

    public function saveFacility()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'image' => $this->isEditing ? 'nullable|image|max:10240' : 'required|image|max:10240',
        ]);

        $imagePath = $this->existing_image;

        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $imagePath = $this->image->store('facilities', 'public');
        }

        if ($this->isEditing) {
            Facility::findOrFail($this->facility_id)->update([
                'name' => $this->name,
                'capacity' => $this->capacity,
                'type' => $this->type,
                'image_path' => $imagePath,
            ]);
            session()->flash('success', 'Facility updated successfully.');
        } else {
            Facility::create([
                'name' => $this->name,
                'capacity' => $this->capacity,
                'type' => $this->type,
                'image_path' => $imagePath,
            ]);
            session()->flash('success', 'Facility created successfully.');
        }

        $this->closeFacilityModal();
    }

    public function deleteFacility($id)
    {
        $facility = Facility::findOrFail($id);
        if ($facility->image_path) {
            Storage::disk('public')->delete($facility->image_path);
        }
        $facility->delete();
        session()->flash('success', 'Facility deleted successfully.');
    }

    // --- Booking Approvals ---
    public function updateBookingStatus($id, $status)
    {
        $booking = FacilityBooking::findOrFail($id);
        $booking->update(['status' => $status]);
        session()->flash('success', "Booking {$status} successfully.");
    }
}; ?>

<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Facilities Management</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage community facilities and approve bookings.</p>
        </div>
        
        <div class="flex gap-2 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
            <button wire:click="setTab('facilities')" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $activeTab === 'facilities' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                Facilities List
            </button>
            <button wire:click="setTab('bookings')" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $activeTab === 'bookings' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                Booking Requests
                @if($pendingBookings->count() > 0)
                    <span class="ml-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingBookings->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($activeTab === 'facilities')
        <!-- Facilities Tab -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">All Facilities</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage the spaces available for community booking.</p>
                </div>
                <button wire:click="openFacilityModal" class="bg-[#6b589e] hover:bg-[#584882] shadow-md shadow-[#6b589e]/20 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Facility
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs w-24">Image</th>
                            <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Name & Details</th>
                            <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Type Label</th>
                            <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($facilities as $facility)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="h-16 w-24 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 shadow-sm transition-transform duration-300 group-hover:scale-105">
                                        @if($facility->image_path)
                                            <img src="{{ Storage::url($facility->image_path) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 dark:text-white text-base mb-1">{{ $facility->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        {{ $facility->capacity }}
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        {{ $facility->type }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openFacilityModal({{ $facility->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 dark:hover:bg-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all duration-200 border border-indigo-100 dark:border-indigo-800 hover:border-transparent" title="Edit Facility">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button wire:click="deleteFacility({{ $facility->id }})" wire:confirm="Are you sure you want to delete this facility?" class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-600 p-2.5 rounded-lg transition-all duration-200 border border-red-100 dark:border-red-800 hover:border-transparent" title="Delete Facility">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No facilities found</h3>
                                        <p class="text-sm text-gray-500">Get started by adding a new space for your community.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- Bookings Tab -->
        <div class="space-y-8">
            <!-- Pending Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-orange-200 dark:border-orange-900/30 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-400"></div>
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-orange-50/30 dark:bg-orange-900/10">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        Pending Booking Requests
                        <span class="bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400 text-sm py-1 px-3 rounded-full font-bold shadow-sm border border-orange-200 dark:border-orange-500/30">{{ $pendingBookings->count() }}</span>
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Request Date</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Resident</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Facility</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Date & Time</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Decide</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($pendingBookings as $booking)
                                <tr class="hover:bg-orange-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                    <td class="px-8 py-5 text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $booking->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white text-base">{{ $booking->user->name }}</div>
                                        <div class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Unit {{ $booking->user->unit_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-5 font-bold text-gray-900 dark:text-white text-base">{{ $booking->facility_name }}</td>
                                    <td class="px-8 py-5">
                                        <div class="text-gray-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-90 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="updateBookingStatus({{ $booking->id }}, 'approved')" class="bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/50 hover:bg-green-600 hover:text-white hover:border-transparent dark:hover:bg-green-500 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Approve
                                            </button>
                                            <button wire:click="updateBookingStatus({{ $booking->id }}, 'rejected')" class="bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800/50 hover:bg-red-500 hover:text-white hover:border-transparent px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600 text-orange-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No pending requests</h3>
                                            <p class="text-sm text-gray-500">All caught up! There are no facilities demanding your approval.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Booking History -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Booking History</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Resident</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Facility</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Date & Time</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Status</th>
                                <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Decided At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($historyBookings as $booking)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white text-base">{{ $booking->user->name }}</div>
                                    </td>
                                    <td class="px-8 py-5 font-medium text-gray-700 dark:text-gray-300">{{ $booking->facility_name }}</td>
                                    <td class="px-8 py-5">
                                        <div class="text-gray-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($booking->status === 'approved')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/50 shadow-sm">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> APPROVED
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800/50 shadow-sm">
                                                <span class="w-2 h-2 bg-red-500 rounded-full"></span> REJECTED
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-sm font-medium text-gray-400 dark:text-gray-500">
                                        {{ $booking->updated_at->format('M d, g:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No history found</h3>
                                            <p class="text-sm text-gray-500">Completed and rejected bookings will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Facility Modal -->
    <!-- Facility Modal -->
    @if($showFacilityModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-lg mb-8 mt-8 border border-gray-100 dark:border-gray-700 relative transform transition-all">
                
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        {{ $isEditing ? 'Edit Facility' : 'Add New Facility' }}
                    </h3>
                    <button wire:click="closeFacilityModal" class="text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all duration-200 hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="saveFacility" class="p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Facility Name</label>
                        <input wire:model="name" type="text" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none" placeholder="e.g. Badminton Court A">
                        @error('name') <span class="text-red-500 text-xs font-semibold mt-1.5 block flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Capacity / Value</label>
                            <input wire:model="capacity" type="text" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none" placeholder="e.g. 30, Internet">
                            @error('capacity') <span class="text-red-500 text-xs font-semibold mt-1.5 block flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Type Label</label>
                            <input wire:model="type" type="text" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-3.5 transition-all outline-none" placeholder="e.g. Capacity, Court, With">
                            @error('type') <span class="text-red-500 text-xs font-semibold mt-1.5 block flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex justify-between items-end">
                            Facility Image
                            <span class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">Optional</span>
                        </label>
                        
                        @if($existing_image && !$image)
                            <div class="mb-4 relative w-full h-40 rounded-2xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-inner group">
                                <img src="{{ Storage::url($existing_image) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-sm font-bold px-4 py-2 bg-black/50 rounded-full backdrop-blur-md">Current Image</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full min-h-[140px] border-2 border-dashed border-gray-300 hover:border-indigo-400 dark:border-gray-600 dark:hover:border-indigo-500 rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/30 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all duration-300 group">
                                <div class="flex flex-col items-center justify-center pt-6 pb-6 text-center px-4">
                                    <div class="w-12 h-12 mb-3 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center shadow-sm group-hover:scale-110 group-hover:border-indigo-200 dark:group-hover:border-indigo-800 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Click to upload image</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">SVG, PNG, JPG or GIF (MAX. 2MB)</p>
                                </div>
                                <input wire:model="image" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                        @if ($image)
                            <div class="mt-4 relative w-full h-40 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 overflow-hidden shadow-inner group">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-sm font-bold px-4 py-2 bg-black/50 rounded-full backdrop-blur-md">New Image Preview</span>
                                </div>
                            </div>
                        @endif
                        @error('image') <span class="text-red-500 text-xs font-semibold mt-1.5 block flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-2">
                        <button type="button" wire:click="closeFacilityModal" class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 outline-none text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#6b589e] hover:bg-[#584882] text-white font-bold rounded-xl shadow-lg shadow-[#6b589e]/30 transition-all duration-300 transform hover:-translate-y-0.5 outline-none flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="saveFacility">
                                {{ $isEditing ? 'Save Changes' : 'Create Facility' }}
                            </span>
                            <span wire:loading wire:target="saveFacility" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
