<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Event;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination, WithFileUploads;

    // Listing state
    public string $search = '';
    public string $statusFilter = 'all';

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?Event $viewingEvent = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $event_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $location = '';
    public string $status = 'approved';
    public $image;
    public ?string $existingImage = null;

    public function with(): array
    {
        $query = Event::with('creator')
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', '%'.$this->search.'%')
                  ->orWhere('location', 'like', '%'.$this->search.'%')
            )
            ->when($this->statusFilter !== 'all', fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->orderBy('event_date', 'desc');

        return [
            'events' => $query->paginate(10),
            'totalEvents'   => Event::count(),
            'pendingCount'  => Event::where('status','pending')->count(),
            'approvedCount' => Event::where('status','approved')->count(),
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    // ─── Open CREATE modal ──────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->status = 'approved';
        $this->showModal = true;
    }

    // ─── Open EDIT modal ────────────────────────────────────────────────────
    public function openEdit(int $id): void
    {
        $event = Event::findOrFail($id);
        $this->editingId     = $id;
        $this->title         = $event->title;
        $this->description   = $event->description;
        $this->event_date    = $event->event_date->format('Y-m-d');
        $this->start_time    = Carbon::parse($event->start_time)->format('H:i');
        $this->end_time      = Carbon::parse($event->end_time)->format('H:i');
        $this->location      = $event->location;
        $this->status        = $event->status;
        $this->existingImage = $event->image_path;
        $this->image         = null;
        $this->showModal     = true;
    }

    // ─── Open VIEW modal ────────────────────────────────────────────────────
    public function openView(int $id): void
    {
        $this->viewingEvent = Event::with('creator')->findOrFail($id);
        $this->showViewModal = true;
    }

    // ─── Save (Create or Update) ─────────────────────────────────────────────
    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'location'    => 'required|string|max:255',
            'status'      => 'required|in:pending,approved,rejected',
            'image'       => 'nullable|image|max:2048',
        ]);

        $imagePath = $this->existingImage;
        if ($this->image) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $this->image->store('event-images', 'public');
        }

        $data = [
            'title'       => $this->title,
            'description' => $this->description,
            'event_date'  => $this->event_date,
            'start_time'  => $this->start_time,
            'end_time'    => $this->end_time,
            'location'    => $this->location,
            'status'      => $this->status,
            'image_path'  => $imagePath,
        ];

        if ($this->editingId) {
            Event::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Event updated successfully.');
            // Push update notification to all residents
            UserNotification::pushToAll(
                'event_updated',
                '📅 Event Updated: ' . $this->title,
                "The event \"{$this->title}\" on " . \Carbon\Carbon::parse($this->event_date)->format('d M Y') . " has been updated."
            );
        } else {
            Event::create(array_merge($data, ['user_id' => auth()->id()]));
            session()->flash('success', 'Event created successfully.');
            // Push new event notification to all residents (only if approved)
            if ($this->status === 'approved') {
                UserNotification::pushToAll(
                    'event_new',
                    '🎉 New Event: ' . $this->title,
                    "A new community event \"{$this->title}\" is happening on " . \Carbon\Carbon::parse($this->event_date)->format('d M Y') . " at {$this->location}."
                );
            }
        }

        $this->resetForm();
        $this->showModal = false;
    }

    // ─── Confirm Delete ──────────────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    // ─── Delete ──────────────────────────────────────────────────────────────
    public function deleteEvent(): void
    {
        $event = Event::findOrFail($this->deletingId);
        if ($event->image_path) Storage::disk('public')->delete($event->image_path);
        $event->delete();
        $this->showDeleteModal = false;
        $this->deletingId = null;
        session()->flash('success', 'Event deleted successfully.');
    }

    // ─── Quick status change ─────────────────────────────────────────────────
    public function setStatus(int $id, string $status): void
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => $status]);
        session()->flash('success', 'Event status updated.');

        if ($status === 'approved') {
            UserNotification::pushToAll(
                'event_approved',
                '✅ Event Approved: ' . $event->title,
                "The community event \"{$event->title}\" on " . $event->event_date->format('d M Y') . " at {$event->location} is now approved."
            );
        }
    }

    private function resetForm(): void
    {
        $this->editingId     = null;
        $this->title         = '';
        $this->description   = '';
        $this->event_date    = '';
        $this->start_time    = '';
        $this->end_time      = '';
        $this->location      = '';
        $this->status        = 'approved';
        $this->image         = null;
        $this->existingImage = null;
        $this->resetValidation();
    }
}; ?>

<div>
    {{-- ── Page Header ── --}}
    <div class="px-6 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Events Management</h1>
            <p class="text-slate-400 text-sm mt-0.5">Create, edit, approve or remove community events.</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:-translate-y-0.5"
            style="background:linear-gradient(135deg,#6366f1,#8b5cf6); box-shadow:0 4px 15px rgba(99,102,241,.35);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </button>
    </div>

    {{-- ── Stats ── --}}
    <div class="px-6 pb-5 grid grid-cols-3 gap-4">
        @foreach([
            ['label'=>'Total Events', 'value'=>$totalEvents, 'color'=>'#6366f1'],
            ['label'=>'Pending', 'value'=>$pendingCount, 'color'=>'#f59e0b'],
            ['label'=>'Approved', 'value'=>$approvedCount, 'color'=>'#10b981'],
        ] as $stat)
        <div class="rounded-2xl border border-slate-700/50 p-4 flex items-center gap-3" style="background:#1e293b;">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                style="background:{{ $stat['color'] }}18; border:1px solid {{ $stat['color'] }}30;">
                <span class="text-base font-extrabold" style="color:{{ $stat['color'] }};">{{ $stat['value'] }}</span>
            </div>
            <span class="text-xs font-semibold text-slate-400">{{ $stat['label'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- ── Flash ── --}}
    @if(session()->has('success'))
        <div class="mx-6 mb-4 px-4 py-3 rounded-xl text-sm font-medium text-emerald-300 flex items-center gap-2"
            style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25);">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Filters ── --}}
    <div class="px-6 pb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by title or location…"
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-700 text-sm text-slate-200 placeholder-slate-500 outline-none focus:border-indigo-500 transition-all"
                style="background:#1e293b;">
        </div>
        <select wire:model.live="statusFilter"
            class="px-4 py-2.5 rounded-xl border border-slate-700 text-sm text-slate-200 outline-none focus:border-indigo-500 transition-all"
            style="background:#1e293b;">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- ── Table ── --}}
    <div class="px-6 pb-10">
        <div class="rounded-2xl border border-slate-700/50 overflow-hidden" style="background:#1e293b;">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-300">
                    <thead class="text-xs uppercase text-slate-500 border-b border-slate-700/50" style="background:#0f172a;">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Event</th>
                            <th class="px-5 py-3.5 font-semibold">Date & Time</th>
                            <th class="px-5 py-3.5 font-semibold">Location</th>
                            <th class="px-5 py-3.5 font-semibold">Created By</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($events as $event)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($event->image_path)
                                            <img src="{{ asset('storage/'.$event->image_path) }}"
                                                class="w-14 h-10 rounded-lg object-cover border border-slate-700 shrink-0">
                                        @else
                                            <div class="w-14 h-10 rounded-lg flex items-center justify-center border border-slate-700 shrink-0"
                                                style="background:rgba(99,102,241,.1);">
                                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-white line-clamp-1">{{ $event->title }}</p>
                                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ Str::limit($event->description, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-slate-200 font-medium">{{ $event->event_date->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ Carbon::parse($event->start_time)->format('h:i A') }} – {{ Carbon::parse($event->end_time)->format('h:i A') }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-400 max-w-[160px]">
                                    <p class="line-clamp-1">{{ $event->location }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-indigo-400 font-medium">{{ $event->creator->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $event->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $sc = ['pending'=>['bg'=>'rgba(245,158,11,.1)','border'=>'rgba(245,158,11,.25)','text'=>'#fbbf24'],
                                               'approved'=>['bg'=>'rgba(16,185,129,.1)','border'=>'rgba(16,185,129,.25)','text'=>'#34d399'],
                                               'rejected'=>['bg'=>'rgba(239,68,68,.1)','border'=>'rgba(239,68,68,.25)','text'=>'#f87171']][$event->status] ?? [];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                        style="background:{{ $sc['bg'] }}; border:1px solid {{ $sc['border'] }}; color:{{ $sc['text'] }};">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $sc['text'] }};"></span>
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <button wire:click="openView({{ $event->id }})"
                                            class="p-2 rounded-lg text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 transition-all" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        {{-- Edit --}}
                                        <button wire:click="openEdit({{ $event->id }})"
                                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        {{-- Approve (if not approved) --}}
                                        @if($event->status !== 'approved')
                                            <button wire:click="setStatus({{ $event->id }}, 'approved')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 transition-all" title="Approve">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Reject (if not rejected) --}}
                                        @if($event->status !== 'rejected')
                                            <button wire:click="setStatus({{ $event->id }}, 'rejected')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 transition-all" title="Reject">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                        {{-- Delete --}}
                                        <button wire:click="confirmDelete({{ $event->id }})"
                                            class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3"
                                        style="background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2);">
                                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-400 font-semibold">No events found</p>
                                    <p class="text-slate-600 text-xs mt-1">Try adjusting your search or create a new event.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
                <div class="px-5 py-4 border-t border-slate-700/50">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         VIEW MODAL
    ════════════════════════════════════════════════════════ --}}
    @if($showViewModal && $viewingEvent)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background:rgba(0,0,0,.65); backdrop-filter:blur(4px);">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-700/60 shadow-2xl overflow-hidden"
                style="background:#1e293b;" wire:click.stop>
                
                {{-- Modal header --}}
                <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between"
                    style="background:#0f172a;">
                    <h3 class="text-base font-bold text-white">Event Details</h3>
                    <button wire:click="$set('showViewModal', false)"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal body --}}
                <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    @if($viewingEvent->image_path)
                        <img src="{{ asset('storage/'.$viewingEvent->image_path) }}" class="w-full h-48 object-cover rounded-xl mb-6 shadow-md border border-slate-700">
                    @endif

                    <h2 class="text-2xl font-bold text-white mb-2">{{ $viewingEvent->title }}</h2>
                    
                    <div class="flex items-center gap-3 mb-6">
                        @php
                            $vsc = ['pending'=>['bg'=>'rgba(245,158,11,.1)','border'=>'rgba(245,158,11,.25)','text'=>'#fbbf24'],
                                    'approved'=>['bg'=>'rgba(16,185,129,.1)','border'=>'rgba(16,185,129,.25)','text'=>'#34d399'],
                                    'rejected'=>['bg'=>'rgba(239,68,68,.1)','border'=>'rgba(239,68,68,.25)','text'=>'#f87171']][$viewingEvent->status] ?? [];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                            style="background:{{ $vsc['bg'] }}; border:1px solid {{ $vsc['border'] }}; color:{{ $vsc['text'] }};">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $vsc['text'] }};"></span>
                            {{ ucfirst($viewingEvent->status) }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">By: {{ $viewingEvent->creator->name ?? 'Admin' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-700/50" style="background:#0f172a;">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2);">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Date & Time</p>
                                <p class="text-sm font-semibold text-slate-200 mt-0.5">{{ $viewingEvent->event_date->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($viewingEvent->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($viewingEvent->end_time)->format('h:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-700/50" style="background:#0f172a;">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.2);">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Location</p>
                                <p class="text-sm font-semibold text-slate-200 mt-0.5">{{ $viewingEvent->location }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description</h4>
                        <div class="p-4 rounded-xl border border-slate-700/50 text-sm text-slate-300 leading-relaxed max-h-40 overflow-y-auto custom-scrollbar" style="background:#0f172a;">
                            {!! nl2br(e($viewingEvent->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         CREATE / EDIT MODAL
    ════════════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/80 backdrop-blur-sm p-4 md:p-6 transition-all duration-300">
            <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 md:p-10 transform transition-all duration-300 overflow-hidden" wire:click.stop>
                
                <!-- Header -->
                <div class="relative z-10 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-6 mb-8">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-400 mb-2">
                            Administrative Console
                        </span>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            📝 {{ $editingId ? 'Edit Event' : 'Create New Event' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Directly manage, customize, and publish community events.</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-700 p-2.5 rounded-full transition-all duration-300 border border-gray-200 dark:border-gray-700 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit="save" class="relative z-10 space-y-8 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                        
                        <!-- Left Column (3/5 cols) -->
                        <div class="lg:col-span-3 space-y-6">
                            <!-- Title -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Title <span class="text-red-500">*</span></label>
                                <input wire:model="title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none" placeholder="Event title">
                                @error('title') <p class="text-red-550 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date & Location -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Date -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date <span class="text-red-500">*</span></label>
                                    <input wire:model="event_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none color-scheme-dark">
                                    @error('event_date') <p class="text-red-550 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                                </div>

                                <!-- Location -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location <span class="text-red-500">*</span></label>
                                    <input wire:model="location" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none" placeholder="e.g. Block A Community Center">
                                    @error('location') <p class="text-red-550 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Timing Card -->
                            <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-250 dark:border-gray-750 rounded-lg p-5 space-y-4">
                                <div class="flex items-center gap-2 text-purple-600 dark:text-purple-400 font-bold text-xs uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Timing Details
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Time <span class="text-red-500">*</span></label>
                                        <input wire:model="start_time" type="time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none color-scheme-dark text-sm">
                                        @error('start_time') <p class="text-red-550 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Time <span class="text-red-500">*</span></label>
                                        <input wire:model="end_time" type="time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none color-scheme-dark text-sm">
                                        @error('end_time') <p class="text-red-550 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status Select -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Publish Status</label>
                                <div class="relative">
                                    <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none appearance-none cursor-pointer">
                                        <option value="approved">Approved / Live</option>
                                        <option value="pending">Pending Admin Review</option>
                                        <option value="rejected">Rejected / Hidden</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column (2/5 cols) -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Image -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Image</label>
                                @if($existingImage && !$image)
                                    <div class="relative group rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-2 transition-all duration-300 shadow-md">
                                        <img src="{{ asset('storage/'.$existingImage) }}" class="object-cover w-full h-[154px] rounded-md shadow-inner">
                                        <div class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <label class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold rounded-lg cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-lg">
                                                Replace Image
                                                <input wire:model="image" type="file" class="hidden" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                @elseif($image)
                                    <div class="relative group rounded-lg overflow-hidden border border-purple-500/30 bg-gray-50 dark:bg-gray-900/60 p-2 transition-all duration-300 shadow-md">
                                        <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-[154px] rounded-md shadow-inner">
                                        <div class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                                            <label class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold rounded-lg cursor-pointer transition-all hover:scale-105 active:scale-95 shadow-lg">
                                                Change
                                                <input wire:model="image" type="file" class="hidden" accept="image/*">
                                            </label>
                                            <button type="button" wire:click="$set('image', null)" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-lg transition-all hover:scale-105 active:scale-95 shadow-lg">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-[176px] border-2 border-dashed border-gray-350 dark:border-gray-700 hover:border-purple-500/50 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700/50 hover:bg-purple-50/10 dark:hover:bg-purple-950/10 transition-all duration-300 group">
                                            <div class="flex flex-col items-center justify-center text-center p-5">
                                                <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-250 dark:border-gray-700 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:border-purple-500/30 transition-all duration-300 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-purple-500"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                </div>
                                                <p class="text-xs font-bold text-gray-600 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Upload poster / photo</p>
                                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-widest">JPG, PNG (Max. 2MB)</p>
                                            </div>
                                            <input wire:model="image" type="file" class="hidden" accept="image/*">
                                        </label>
                                    </div>
                                @endif
                                @error('image') <p class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                                <textarea wire:model="description" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white outline-none resize-none" placeholder="Provide details about the event…"></textarea>
                                @error('description') <p class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-6 flex gap-4 justify-end border-t border-gray-250 dark:border-gray-750 mt-8">
                        <button type="button" wire:click="$set('showModal', false)" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 border border-gray-250 dark:border-gray-600 rounded-lg font-semibold text-sm transition-all duration-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition duration-300 shadow-md flex items-center gap-2">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>{{ $editingId ? 'Update Event' : 'Create Event' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         DELETE CONFIRM MODAL
    ════════════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background:rgba(0,0,0,.65); backdrop-filter:blur(4px);">
            <div class="w-full max-w-sm rounded-2xl border border-slate-700/60 shadow-2xl p-6 text-center"
                style="background:#1e293b;">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
                    style="background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.25);">
                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Delete Event?</h3>
                <p class="text-sm text-slate-400 mb-6">This action cannot be undone. The event and its image will be permanently removed.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-300 border border-slate-600 hover:bg-white/5 transition-all">
                        Cancel
                    </button>
                    <button wire:click="deleteEvent"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                        style="background:rgba(239,68,68,.8); border:1px solid rgba(239,68,68,.4);">
                        <span wire:loading.remove wire:target="deleteEvent">Delete</span>
                        <span wire:loading wire:target="deleteEvent">Deleting…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
