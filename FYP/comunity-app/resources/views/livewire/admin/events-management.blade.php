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
    public ?int $editingId = null;
    public ?int $deletingId = null;

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
         CREATE / EDIT MODAL
    ════════════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background:rgba(0,0,0,.65); backdrop-filter:blur(4px);">
            <div class="w-full max-w-xl rounded-2xl border border-slate-700/60 shadow-2xl overflow-hidden"
                style="background:#1e293b;" wire:click.stop>

                {{-- Modal header --}}
                <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between"
                    style="background:#0f172a;">
                    <h3 class="text-base font-bold text-white">
                        {{ $editingId ? 'Edit Event' : 'Create New Event' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal body --}}
                <form wire:submit="save" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                    @php $inputClass = "w-full px-4 py-2.5 rounded-xl border border-slate-600 text-slate-100 text-sm placeholder-slate-500 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"; $inputStyle = "background:#0f172a;"; @endphp

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Title *</label>
                        <input wire:model="title" type="text" placeholder="Event title" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                        @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Description *</label>
                        <textarea wire:model="description" rows="3" placeholder="Describe the event…"
                            class="{{ $inputClass }}" style="{{ $inputStyle }} resize-none;"></textarea>
                        @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Date *</label>
                            <input wire:model="event_date" type="date" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                            @error('event_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Start *</label>
                            <input wire:model="start_time" type="time" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                            @error('start_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">End *</label>
                            <input wire:model="end_time" type="time" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                            @error('end_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Location *</label>
                        <input wire:model="location" type="text" placeholder="e.g. Community Hall, Block A" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                        @error('location') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Status</label>
                        <select wire:model="status" class="{{ $inputClass }}" style="{{ $inputStyle }}">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    {{-- Image Upload --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Event Image</label>
                        @if($existingImage && !$image)
                            <div class="mb-2 flex items-center gap-3 p-2 rounded-lg border border-slate-700" style="background:#0f172a;">
                                <img src="{{ asset('storage/'.$existingImage) }}" class="w-16 h-10 rounded-lg object-cover">
                                <p class="text-xs text-slate-400">Current image</p>
                            </div>
                        @endif
                        @if($image)
                            <div class="mb-2 flex items-center gap-3 p-2 rounded-lg border border-indigo-700/40" style="background:#0f172a;">
                                <img src="{{ $image->temporaryUrl() }}" class="w-16 h-10 rounded-lg object-cover">
                                <p class="text-xs text-slate-400">New image preview</p>
                            </div>
                        @endif
                        <input wire:model="image" type="file" accept="image/*"
                            class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:text-white file:cursor-pointer transition-all"
                            style="file-bg:rgba(99,102,241,.8);">
                        @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 border border-slate-600 hover:bg-white/5 transition-all">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#6366f1,#8b5cf6); box-shadow:0 4px 15px rgba(99,102,241,.35);">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Event' : 'Create Event' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
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
