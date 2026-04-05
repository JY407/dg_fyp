<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\FacilityBooking;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app')] class extends Component {
    public $showBookingModal = false;
    public $selectedFacility = null;
    public $booking_date;
    public $start_time;
    public $end_time;

    // Calendar properties
    public $currentMonth;
    public $currentYear;
    public $calendarDays = [];
    public $recentBookings = [];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->generateCalendar();
        
        if (auth()->check()) {
            $this->recentBookings = auth()->user()->facilityBookings()
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }
    }

    public function with()
    {
        return [
            'facilities' => Facility::all(),
        ];
    }

    public function generateCalendar()
    {
        $this->calendarDays = [];
        
        $firstDayOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
        
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->calendarDays[] = ['day' => '', 'date' => null, 'hasEvent' => false];
        }
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, $i)->format('Y-m-d');
            
            $hasEvent = false;
            if (auth()->check()) {
                $hasEvent = auth()->user()->facilityBookings()->whereDate('booking_date', $date)->exists();
            }
            
            $this->calendarDays[] = [
                'day' => $i, 
                'date' => $date, 
                'hasEvent' => $hasEvent,
                'isToday' => $date === now()->format('Y-m-d')
            ];
        }
    }
    
    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->generateCalendar();
    }
    
    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->generateCalendar();
    }

    public function openBookingModal($facilityName)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }
        $this->selectedFacility = $facilityName;
        $this->showBookingModal = true;
    }

    public function submitBooking()
    {
        $this->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        auth()->user()->facilityBookings()->create([
            'facility_name' => $this->selectedFacility,
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => 'pending',
        ]);

        $this->reset(['selectedFacility', 'booking_date', 'start_time', 'end_time', 'showBookingModal']);
        $this->mount();
        session()->flash('success', 'Facility booked successfully!');
    }
}; ?>

<div class="min-h-screen bg-gray-900 text-gray-100 font-sans relative overflow-hidden">
    <!-- Background Decor elements -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-900/30 to-gray-900 pointer-events-none"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-40 -left-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-[1400px] mx-auto px-6 lg:px-8 py-12 relative z-10">
        <!-- Header -->
        <div class="mb-12 text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400 mb-2">
                    Community Facilities
                </h1>
                <p class="text-gray-400 text-lg">Book premium spaces for your events and activities.</p>

            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-8 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-2xl flex items-center shadow-[0_0_15px_rgba(74,222,128,0.1)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- Left Side: Facilities List -->
            <div class="xl:col-span-2 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold flex items-center gap-3">
                        <span class="bg-indigo-500/20 p-2 rounded-xl text-indigo-400 border border-indigo-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/><rect x="7" y="9" width="2" height="2"/><rect x="15" y="9" width="2" height="2"/><rect x="7" y="13" width="2" height="2"/><rect x="15" y="13" width="2" height="2"/></svg>
                        </span>
                        Available Spaces
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($facilities as $facility)
                        <div class="group relative bg-gray-800/60 backdrop-blur-xl rounded-3xl overflow-hidden border border-gray-700/50 hover:border-indigo-500/50 transition-all duration-500 hover:shadow-[0_10px_40px_rgba(79,70,229,0.15)] flex flex-col h-full transform hover:-translate-y-1">
                            <!-- Image Container -->
                            <div class="h-48 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-90 z-10 transition-opacity group-hover:opacity-70"></div>
                                @if($facility->image_path)
                                    <img src="{{ Storage::url($facility->image_path) }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out">
                                @else
                                    <div class="w-full h-full bg-gray-700 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-700 ease-in-out">
                                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                <div class="absolute bottom-4 left-4 z-20">
                                    <div class="bg-gray-900/80 backdrop-blur-md border border-gray-700/50 px-3 py-1.5 rounded-lg inline-flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        <span class="text-xs font-bold text-gray-200">{{ $facility->capacity }} {{ $facility->type }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 relative z-20 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors">{{ $facility->name }}</h3>
                                <p class="text-gray-400 text-sm mb-6 flex-1">Perfect space for your community activities, fully equipped for your needs.</p>
                                
                                <button wire:click="openBookingModal('{{ $facility->name }}')" class="w-full py-3 px-4 bg-gray-900/50 hover:bg-indigo-600 border border-gray-700 hover:border-indigo-500 text-gray-300 hover:text-white font-semibold rounded-xl transition-all duration-300 flex justify-center items-center gap-2 group-hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Side: Calendar & Recent Activity -->
            <div class="xl:col-span-1 flex flex-col gap-8 pb-8">
                <!-- Calendar Widget -->
                <div class="bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-gray-700/50 shadow-2xl overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none"></div>
                    
                    <div class="p-6 border-b border-gray-700/50 bg-gray-900/30">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-white text-lg flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}
                            </h3>
                            <div class="flex gap-1 bg-gray-900 rounded-lg p-1 border border-gray-700">
                                <button wire:click="previousMonth" class="p-1.5 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                </button>
                                <button wire:click="nextMonth" class="p-1.5 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-7 gap-1 text-center mb-3">
                            @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $day }}</div>
                            @endforeach
                        </div>
                        
                        <div class="grid grid-cols-7 gap-1 text-center text-sm">
                            @foreach($calendarDays as $day)
                                <div class="py-1">
                                    @if($day['day'] !== '')
                                        <div class="w-8 h-8 flex items-center justify-center mx-auto rounded-full font-medium transition-all
                                            {{ $day['isToday'] ? 'bg-indigo-600 text-white shadow-[0_0_10px_rgba(79,70,229,0.5)]' : 'text-gray-300 hover:bg-gray-700 hover:text-white cursor-pointer' }}
                                            {{ $day['hasEvent'] ? 'ring-2 ring-teal-500 ring-offset-2 ring-offset-gray-800' : '' }}
                                        ">
                                            {{ $day['day'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-gray-700/50 p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl -mr-16 -mt-16 transition-all group-hover:bg-teal-500/20"></div>
                    
                    <h3 class="font-bold text-white text-lg mb-6 flex items-center gap-2 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Your Bookings
                    </h3>
                    
                    <div class="space-y-4 relative z-10">
                        @forelse($recentBookings as $booking)
                            <div class="bg-gray-900/50 border border-gray-700/50 rounded-xl p-4 hover:border-teal-500/30 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-gray-200 font-bold text-sm">{{ $booking->facility_name }}</span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-{{ $booking->status === 'approved' ? 'green' : ($booking->status === 'rejected' ? 'red' : 'yellow') }}-500/20 text-{{ $booking->status === 'approved' ? 'green' : ($booking->status === 'rejected' ? 'red' : 'yellow') }}-400 border border-{{ $booking->status === 'approved' ? 'green' : ($booking->status === 'rejected' ? 'red' : 'yellow') }}-500/30">
                                        {{ $booking->status }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 bg-gray-900/50 border border-gray-700/50 rounded-xl p-6 text-center border-dashed">
                                No recent bookings found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    @if($showBookingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/80 backdrop-blur-sm p-4 sm:p-0">
            <div class="relative w-full max-w-lg bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 p-8 transform transition-all animate-fade-in-up">
                
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex justify-between items-start mb-6 border-b border-gray-700/50 pb-6 relative z-10">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-1">Book Facility</h3>
                        <p class="text-indigo-400 text-sm font-medium">{{ $selectedFacility }}</p>
                    </div>
                    <button wire:click="$toggle('showBookingModal')" class="text-gray-500 hover:text-white bg-gray-900 hover:bg-gray-700 p-2 rounded-full transition-colors border border-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <form wire:submit="submitBooking" class="space-y-6 relative z-10">
                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2">Select Date</label>
                        <input wire:model="booking_date" type="date" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                        @error('booking_date') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-300 mb-2">Start Time</label>
                            <input wire:model="start_time" type="time" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                            @error('start_time') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-300 mb-2">End Time</label>
                            <input wire:model="end_time" type="time" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                            @error('end_time') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-6 mt-2 border-t border-gray-700/50">
                        <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.4)] transition-all flex justify-center items-center gap-2">
                            <svg wire:loading wire:target="submitBooking" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Confirm Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
