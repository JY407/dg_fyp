<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use Livewire\WithFileUploads;
use Carbon\Carbon;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public $title;
    public $description;
    public $event_date;
    public $start_time;
    public $end_time;
    public $location;
    public $image;

    public $showCreateModal = false;
    
    // Calendar properties
    public $currentMonth;
    public $currentYear;
    public $calendarDays = [];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->generateCalendar();
    }

    public function with()
    {
        return [
            'approvedEvents' => Event::where('status', 'approved')->orderBy('event_date', 'asc')->get(),
            'joinedEvents' => auth()->check() ? auth()->user()->joinedEvents : collect([]),
        ];
    }
    
    public function generateCalendar()
    {
        $this->calendarDays = [];
        
        $firstDayOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        // Setup start of the calendar grid (0 = Sunday, 1 = Monday, etc.)
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
        
        // Add empty slots for days before the 1st of the month
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->calendarDays[] = ['day' => '', 'date' => null, 'hasEvent' => false, 'events' => []];
        }
        
        // Add the actual days of the month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, $i)->format('Y-m-d');
            
            // Check if user has joined events on this date
            $eventsOnThisDay = [];
            $hasEvent = false;
            
            if (auth()->check()) {
                $eventsOnThisDay = auth()->user()->joinedEvents()
                    ->whereDate('event_date', $date)
                    ->get()
                    ->toArray();
                
                $hasEvent = count($eventsOnThisDay) > 0;
            }
            
            $this->calendarDays[] = [
                'day' => $i, 
                'date' => $date, 
                'hasEvent' => $hasEvent,
                'events' => $eventsOnThisDay,
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

    public function createEvent()
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('event-images', 'public');
        }

        auth()->user()->events()->create([
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'image_path' => $imagePath,
            'status' => 'pending', // Requires admin verification
        ]);

        $this->reset(['title', 'description', 'event_date', 'start_time', 'end_time', 'location', 'image', 'showCreateModal']);
        session()->flash('success', 'Event created successfully and is waiting for admin approval.');
    }

    public function joinEvent(Event $event)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        if (!$event->participants()->where('user_id', auth()->id())->exists()) {
            $event->participants()->attach(auth()->id());
            session()->flash('success', 'You have successfully joined the event!');
            $this->generateCalendar(); // Refresh calendar to show new event
        } else {
            session()->flash('error', 'You have already joined this event.');
        }
    }

    public function leaveEvent(Event $event)
    {
        if (!auth()->check()) {
            return;
        }

        $event->participants()->detach(auth()->id());
        session()->flash('success', 'You have left the event.');
        $this->generateCalendar(); // Refresh calendar
    }
}; ?>

<div class="py-12 bg-gray-900 min-h-screen relative overflow-hidden">
    <!-- Background Decor elements -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-900/20 to-gray-900 pointer-events-none"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-40 -left-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-indigo-500 mb-4 animate-fade-in-down">
                Community Events
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Discover and join upcoming events in our community.
            </p>
            @auth
                <div class="mt-8 flex justify-center">
                    <button wire:click="$toggle('showCreateModal')" class="bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white border border-indigo-500/30 px-6 py-3 rounded-full font-medium shadow-[0_0_15px_rgba(79,70,229,0.2)] transition-all duration-300 flex items-center gap-2 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:rotate-90 transition-transform"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Create Event
                    </button>
                </div>
            @endauth
        </div>

        @if (session()->has('success'))
            <div class="mb-8 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-2xl flex items-center max-w-3xl mx-auto auto-dismiss">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-8 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-2xl flex items-center max-w-3xl mx-auto auto-dismiss">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Left side (Events List) - takes up 3 columns on extra large screens -->
            <div class="xl:col-span-3 space-y-12">
                
                <!-- Joined Events Section -->
                @auth
                    @if($joinedEvents->count() > 0)
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                                <span class="bg-green-500/20 p-2 rounded-xl text-green-400 border border-green-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                </span>
                                My Registered Events
                            </h2>
                            <div class="flex flex-nowrap overflow-x-auto gap-6 pb-4 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
                                @foreach($joinedEvents as $event)
                                    <div class="flex-none w-[320px] bg-gray-800 rounded-2xl border border-gray-700 shadow-lg relative overflow-hidden group hover:border-green-500/50 transition-all duration-300 hover:-translate-y-1">
                                        <div class="px-6 py-5">
                                            <div class="flex justify-between items-start mb-4">
                                                <div class="bg-gray-900 border border-gray-700 text-green-400 px-3 py-1.5 rounded-xl text-center shadow-inner">
                                                    <div class="text-[10px] uppercase tracking-wider font-bold">{{ $event->event_date->format('M') }}</div>
                                                    <div class="text-xl font-black leading-none mt-1">{{ $event->event_date->format('d') }}</div>
                                                </div>
                                            </div>
                                            <h3 class="font-bold text-lg text-white mb-3 line-clamp-1 group-hover:text-green-400 transition-colors">{{ $event->title }}</h3>
                                            <div class="space-y-2 text-xs text-gray-400 mb-4">
                                                <div class="flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                    {{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-teal-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    <span class="line-clamp-1">{{ $event->location }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-6 py-3 bg-gray-900/50 border-t border-gray-700 mt-auto">
                                            <button wire:click="leaveEvent({{ $event->id }})" class="w-full py-1.5 text-gray-400 hover:text-red-400 text-sm font-medium transition-colors flex items-center justify-center gap-2 bg-gray-800 rounded-lg border border-gray-700 hover:border-red-500/30 hover:bg-red-500/10">
                                                Leave
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- All Approved Events Section -->
                <div>
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="bg-indigo-500/20 p-2 rounded-xl text-indigo-400 border border-indigo-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </span>
                        Upcoming Events
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
                        @forelse($approvedEvents as $event)
                            <div class="group relative bg-gray-800 rounded-2xl overflow-hidden border border-gray-700 hover:border-indigo-500/50 transition-all duration-300 hover:shadow-[0_0_30px_rgba(79,70,229,0.15)] flex flex-col h-full">
                                <!-- Image Container -->
                                <div class="h-48 overflow-hidden relative">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-80 z-10"></div>
                                    @if($event->image_path)
                                        <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Date Badge -->
                                    <div class="absolute top-4 right-4 bg-gray-900/80 backdrop-blur-md border border-gray-700 px-3 py-2 rounded-xl text-center z-20 shadow-lg">
                                        <div class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">{{ $event->event_date->format('M') }}</div>
                                        <div class="text-xl font-black text-white leading-none mt-1">{{ $event->event_date->format('d') }}</div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5 relative z-20 flex-1 flex flex-col pt-4">
                                    <h3 class="text-xl font-bold text-white mb-4 group-hover:text-indigo-400 transition-colors line-clamp-2">{{ $event->title }}</h3>
                                    
                                    <div class="space-y-2.5 text-xs text-gray-400 mb-4 pb-4 border-b border-gray-700/50">
                                        <div class="flex items-start gap-3">
                                            <div class="bg-gray-900 p-1.5 rounded-lg border border-gray-700 shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            </div>
                                            <span class="mt-0.5 font-medium">{{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}</span>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="bg-gray-900 p-1.5 rounded-lg border border-gray-700 shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-teal-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                            </div>
                                            <span class="mt-0.5 line-clamp-1">{{ $event->location }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-gray-900 p-1.5 rounded-lg border border-gray-700 shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-400"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                            </div>
                                            <span>{{ $event->participants->count() }} attending</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-400 text-sm line-clamp-2 mb-6 flex-1">{{ $event->description }}</p>

                                    <div class="mt-auto">
                                        @if(auth()->check() && $joinedEvents->contains($event->id))
                                            <button wire:click="leaveEvent({{ $event->id }})" class="w-full py-2 px-4 bg-gray-900 hover:bg-gray-700 border border-gray-700 hover:border-red-500/50 hover:text-red-400 text-gray-300 font-semibold rounded-xl transition-colors flex justify-center items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                Leave
                                            </button>
                                        @else
                                            <button wire:click="joinEvent({{ $event->id }})" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.3)] transition-all flex justify-center items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                                Join Event
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-16 text-center bg-gray-800 border border-gray-700 rounded-2xl">
                                <div class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center mb-4 border border-gray-700 shadow-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <h3 class="font-bold text-gray-300 mb-2">No Upcoming Events</h3>
                                <p class="text-sm text-gray-500 max-w-sm mb-4">There are currently no approved upcoming events in the community.</p>
                                @auth
                                    <button wire:click="$toggle('showCreateModal')" class="text-indigo-400 hover:text-indigo-300 font-medium text-sm">
                                        Be the first to create one
                                    </button>
                                @endauth
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Right side (Calendar Widget) -->
            <div class="xl:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-gray-800 rounded-3xl border border-gray-700 shadow-[0_0_30px_rgba(0,0,0,0.3)] overflow-hidden">
                        <div class="bg-gradient-to-br from-indigo-900/50 to-gray-900 border-b border-gray-700 p-6">
                            <h3 class="font-bold text-lg text-white mb-1">My Schedule</h3>
                            <p class="text-xs text-indigo-300">Your registered events</p>
                        </div>
                        
                        <div class="p-6">
                            <!-- Calendar Header -->
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-white text-lg">
                                    {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}
                                </h4>
                                <div class="flex gap-1">
                                    <button wire:click="previousMonth" class="p-1.5 rounded-lg bg-gray-900 text-gray-400 hover:text-white hover:bg-gray-700 border border-gray-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    </button>
                                    <button wire:click="nextMonth" class="p-1.5 rounded-lg bg-gray-900 text-gray-400 hover:text-white hover:bg-gray-700 border border-gray-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                                    <div class="text-center text-[10px] font-bold text-gray-500 uppercase py-2">{{ $day }}</div>
                                @endforeach
                            </div>
                            
                            <div class="grid grid-cols-7 gap-1">
                                @foreach($calendarDays as $day)
                                    <div class="relative pt-[100%]">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            @if($day['day'] !== '')
                                                <div class="w-8 h-8 flex items-center justify-center rounded-full text-sm transition-all relative group cursor-default
                                                    {{ $day['isToday'] ? 'bg-indigo-600/30 text-indigo-400 font-bold border border-indigo-500/50' : 'text-gray-300 hover:bg-gray-700' }}
                                                    {{ $day['hasEvent'] ? 'font-bold' : '' }}
                                                ">
                                                    {{ $day['day'] }}
                                                    
                                                    <!-- Event Indicator Dot -->
                                                    @if($day['hasEvent'])
                                                        <div class="absolute bottom-1 w-1 h-1 rounded-full bg-green-400 shadow-[0_0_5px_rgba(74,222,128,0.8)]"></div>
                                                        
                                                        <!-- Tooltip for events -->
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 bg-gray-900 border border-gray-700 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-30 p-3 pointer-events-none">
                                                            <div class="text-xs font-bold text-white mb-2">{{ \Carbon\Carbon::parse($day['date'])->format('D, M j') }}</div>
                                                            <div class="space-y-2">
                                                                @foreach($day['events'] as $e)
                                                                    <div class="text-[10px] bg-green-500/10 border border-green-500/20 text-green-300 rounded px-2 py-1 truncate">
                                                                        {{ $e['title'] }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <!-- Tooltip Arrow -->
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-700"></div>
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1.5 border-4 border-transparent border-t-gray-900"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(auth()->check() && count($joinedEvents) == 0)
                                <div class="mt-8 text-center bg-gray-900/50 rounded-xl p-4 border border-gray-700 border-dashed">
                                    <p class="text-sm text-gray-500">You haven't joined any events yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Event Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/80 backdrop-blur-sm p-4 sm:p-0">
                <div class="relative w-full max-w-2xl bg-gray-800 rounded-3xl shadow-2xl border border-gray-700 p-8 transform transition-all">
                    <div class="flex justify-between items-center border-b border-gray-700 pb-5 mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-white">Create New Event</h3>
                            <p class="text-sm text-gray-400 mt-1">Submit an event for admin approval.</p>
                        </div>
                        <button wire:click="$toggle('showCreateModal')" class="text-gray-500 hover:text-gray-300 bg-gray-900 hover:bg-gray-700 p-2 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="createEvent" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Event Title</label>
                                <input wire:model="title" type="text" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white placeholder-gray-500 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3" placeholder="e.g. Weekend Yoga Class">
                                @error('title') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Date</label>
                                <input wire:model="event_date" type="date" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                                @error('event_date') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Location</label>
                                <input wire:model="location" type="text" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white placeholder-gray-500 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3" placeholder="e.g. Community Center Hall A">
                                @error('location') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Start Time -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Start Time</label>
                                <input wire:model="start_time" type="time" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                                @error('start_time') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Time -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">End Time</label>
                                <input wire:model="end_time" type="time" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3 color-scheme-dark">
                                @error('end_time') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Image -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Event Image (Optional)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 border-dashed rounded-xl cursor-pointer bg-gray-900 hover:bg-gray-800 hover:border-indigo-500 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg aria-hidden="true" class="w-8 h-8 text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p class="text-sm text-gray-500 font-medium">Click to upload image</p>
                                        </div>
                                        <input wire:model="image" type="file" class="hidden" accept="image/*">
                                    </label>
                                </div>
                                @if ($image)
                                    <div class="mt-4 relative rounded-xl overflow-hidden h-32 w-1/2 border border-gray-700">
                                        <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                    </div>
                                @endif
                                @error('image') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Description</label>
                                <textarea wire:model="description" rows="4" class="w-full rounded-xl border-gray-600 bg-gray-900 text-white placeholder-gray-500 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 p-3" placeholder="Provide details about the event..."></textarea>
                                @error('description') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3 justify-end border-t border-gray-700 mt-8">
                            <button type="button" wire:click="$toggle('showCreateModal')" class="px-6 py-2.5 bg-gray-800 border border-gray-600 rounded-xl text-gray-300 font-bold hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-[0_0_10px_rgba(79,70,229,0.3)] transition-all flex items-center gap-2">
                                <svg wire:loading wire:target="createEvent" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
<script>
    // Auto clear alert after 5s
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('request', ({ component, succeed }) => {
            succeed(() => {
                setTimeout(() => {
                    document.querySelectorAll('.auto-dismiss').forEach(el => {
                        el.style.opacity = '0';
                        el.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => el.remove(), 500);
                    });
                }, 5000);
            })
        })
    });
</script>
