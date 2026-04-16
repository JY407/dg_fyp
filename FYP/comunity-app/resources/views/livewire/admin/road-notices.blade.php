<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\RoadNotice;
use App\Models\UserNotification;
use Illuminate\Support\Str;

new #[Layout('layouts.admin')] class extends Component {
    use WithFileUploads;

    // Modal state
    public bool $showModal   = false;
    public bool $isEditing   = false;
    public ?int $noticeId    = null;

    // Form fields
    public string $title       = '';
    public string $description = '';
    public string $location    = '';
    public string $notice_type = 'Obstruction';
    public string $severity    = 'Medium';
    public string $status      = 'Active';
    public string $starts_at   = '';
    public string $ends_at     = '';
    public $image;

    // Filter
    public string $filterStatus = 'all';

    public array $noticeTypes = ['Obstruction', 'Road Closure', 'Detour', 'Maintenance', 'Event Setup'];
    public array $severities  = ['Low', 'Medium', 'High'];

    public function with(): array
    {
        $query = RoadNotice::with('poster')->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return [
            'notices' => $query->paginate(10),
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['noticeId', 'title', 'description', 'location', 'image', 'starts_at', 'ends_at']);
        $this->notice_type = 'Obstruction';
        $this->severity    = 'Medium';
        $this->status      = 'Active';
        $this->starts_at   = now()->format('Y-m-d\TH:i');
        $this->isEditing   = false;
        $this->showModal   = true;
    }

    public function openEditModal(int $id): void
    {
        $notice = RoadNotice::findOrFail($id);

        $this->noticeId    = $notice->id;
        $this->title       = $notice->title;
        $this->description = $notice->description;
        $this->location    = $notice->location;
        $this->notice_type = $notice->notice_type;
        $this->severity    = $notice->severity;
        $this->status      = $notice->status;
        $this->starts_at   = $notice->starts_at ? $notice->starts_at->format('Y-m-d\TH:i') : '';
        $this->ends_at     = $notice->ends_at   ? $notice->ends_at->format('Y-m-d\TH:i')   : '';
        $this->image       = null;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'notice_type' => 'required|string',
            'severity'    => 'required|string',
            'status'      => 'required|string',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'image'       => 'nullable|image|max:8192',
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('road-notices', 'public');
        }

        $data = [
            'posted_by'   => auth()->id(),
            'title'       => $this->title,
            'description' => $this->description,
            'location'    => $this->location,
            'notice_type' => $this->notice_type,
            'severity'    => $this->severity,
            'status'      => $this->status,
            'starts_at'   => $this->starts_at ?: null,
            'ends_at'     => $this->ends_at   ?: null,
        ];

        if ($imagePath) {
            $data['image_path'] = $imagePath;
        }

        RoadNotice::updateOrCreate(['id' => $this->noticeId], $data);

        // Notify all residents when a NEW notice is created
        if (!$this->isEditing) {
            $icon = match($this->notice_type) {
                'Road Closure' => '🚧',
                'Detour'       => '🔀',
                'Maintenance'  => '🔧',
                'Event Setup'  => '⛺',
                default        => '⚠️',
            };

            UserNotification::pushToAll(
                'road_notice',
                "{$icon} Road Notice: {$this->title}",
                "Security has reported a {$this->notice_type} at {$this->location}. " . Str::limit($this->description, 80)
            );
        }

        $this->closeModal();
        session()->flash('success', $this->isEditing ? 'Notice updated successfully.' : 'Road notice posted and residents notified.');
    }

    public function toggleStatus(int $id): void
    {
        $notice = RoadNotice::findOrFail($id);
        $notice->update(['status' => $notice->status === 'Active' ? 'Resolved' : 'Active']);
        session()->flash('success', 'Status updated.');
    }

    public function delete(int $id): void
    {
        RoadNotice::findOrFail($id)->delete();
        session()->flash('success', 'Notice deleted.');
    }
}; ?>

<div class="p-6">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Road Notices</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Post road obstructions, closures, and hazards for residents.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{-- Status Filter --}}
            <select wire:model.live="filterStatus"
                class="rounded-xl border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm px-3 py-2.5 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                <option value="all">All Notices</option>
                <option value="Active">Active</option>
                <option value="Resolved">Resolved</option>
            </select>
            <button wire:click="openCreateModal"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-md transition-all duration-200 hover:-translate-y-0.5"
                style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,0.35);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Post Road Notice
            </button>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Notice Cards Grid --}}
    @if ($notices->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
            @foreach ($notices as $notice)
                @php
                    $severityConfig = match($notice->severity) {
                        'High'   => ['bg' => 'rgba(239,68,68,0.12)',  'text' => '#f87171', 'border' => 'rgba(239,68,68,0.25)',  'dot' => '#ef4444'],
                        'Medium' => ['bg' => 'rgba(245,158,11,0.12)', 'text' => '#fbbf24', 'border' => 'rgba(245,158,11,0.25)', 'dot' => '#f59e0b'],
                        default  => ['bg' => 'rgba(34,197,94,0.12)',  'text' => '#4ade80', 'border' => 'rgba(34,197,94,0.25)',  'dot' => '#22c55e'],
                    };
                    $typeIcon = match($notice->notice_type) {
                        'Road Closure' => '🚧',
                        'Detour'       => '🔀',
                        'Maintenance'  => '🔧',
                        'Event Setup'  => '⛺',
                        default        => '⚠️',
                    };
                    $isActive = $notice->status === 'Active';
                @endphp

                <div class="rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5"
                     style="background: #1e293b; border: 1px solid {{ $isActive ? 'rgba(245,158,11,0.2)' : 'rgba(255,255,255,0.06)' }}; box-shadow: {{ $isActive ? '0 0 0 1px rgba(245,158,11,0.1)' : 'none' }};">

                    {{-- Optional image --}}
                    @if ($notice->image_path)
                        <div class="w-full h-40 overflow-hidden">
                            <img src="{{ asset('storage/' . $notice->image_path) }}" alt="Notice image"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-full h-20 flex items-center justify-center text-4xl"
                             style="background: rgba(245,158,11,0.07);">
                            {{ $typeIcon }}
                        </div>
                    @endif

                    <div class="p-5">
                        {{-- Status + Severity row --}}
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background: {{ $severityConfig['bg'] }}; color: {{ $severityConfig['text'] }}; border: 1px solid {{ $severityConfig['border'] }};">
                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background: {{ $severityConfig['dot'] }};"></span>
                                {{ $notice->severity }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $isActive ? 'bg-amber-900/30 text-amber-400 border border-amber-800/40' : 'bg-gray-700/50 text-gray-400 border border-gray-600/40' }}">
                                {{ $notice->status }}
                            </span>
                            <span class="ml-auto text-xs text-gray-500">{{ $notice->notice_type }}</span>
                        </div>

                        {{-- Title --}}
                        <h3 class="font-bold text-white text-base mb-1 leading-tight">{{ $notice->title }}</h3>

                        {{-- Location --}}
                        <div class="flex items-center gap-1.5 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs text-amber-300 font-semibold">{{ $notice->location }}</span>
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-400 leading-relaxed mb-4 line-clamp-2">{{ $notice->description }}</p>

                        {{-- Time --}}
                        @if ($notice->starts_at)
                            <div class="flex items-center gap-1.5 mb-3 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                {{ $notice->starts_at->format('d M Y, h:i A') }}
                                @if ($notice->ends_at)
                                    → {{ $notice->ends_at->format('d M Y, h:i A') }}
                                @endif
                            </div>
                        @endif

                        {{-- Posted by --}}
                        <div class="text-xs text-gray-500 mb-4">
                            Posted by <span class="text-gray-300 font-semibold">{{ $notice->poster->name ?? 'Security' }}</span>
                            · {{ $notice->created_at->diffForHumans() }}
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-white/5">
                            <button wire:click="toggleStatus({{ $notice->id }})"
                                class="flex-1 py-2 rounded-lg text-xs font-bold transition-all duration-200
                                    {{ $isActive ? 'bg-green-900/30 text-green-400 hover:bg-green-800/50 border border-green-800/40'
                                                 : 'bg-amber-900/30 text-amber-400 hover:bg-amber-800/50 border border-amber-800/40' }}">
                                {{ $isActive ? '✓ Mark Resolved' : '↺ Re-activate' }}
                            </button>
                            <button wire:click="openEditModal({{ $notice->id }})"
                                class="px-3 py-2 rounded-lg text-xs font-bold bg-indigo-900/30 text-indigo-400 hover:bg-indigo-800/50 border border-indigo-800/40 transition-all">
                                Edit
                            </button>
                            <button wire:click="delete({{ $notice->id }})"
                                wire:confirm="Delete this road notice?"
                                class="px-3 py-2 rounded-lg text-xs font-bold bg-red-900/30 text-red-400 hover:bg-red-800/50 border border-red-800/40 transition-all">
                                Del
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($notices->hasPages())
            <div class="mt-4">{{ $notices->links() }}</div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 px-8 py-20 text-center">
            <div class="text-6xl mb-4">🚧</div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-200 mb-2">No Road Notices Yet</h3>
            <p class="text-sm text-gray-500 mb-6">Post a road notice to alert residents about obstructions or closures in the neighbourhood.</p>
            <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
                style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                Post First Notice
            </button>
        </div>
    @endif

    {{-- ===== CREATE / EDIT MODAL ===== --}}
    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/70 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-2xl my-8 border border-gray-100 dark:border-gray-700 relative">

                {{-- Modal Header --}}
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        {{ $isEditing ? 'Edit Road Notice' : 'Post Road Notice' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notice Title <span class="text-red-400">*</span></label>
                        <input type="text" wire:model="title"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all"
                            placeholder="e.g. Tent Blocking Jalan Setia 2 — Cannot Cross">
                        @error('title') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Road / Location <span class="text-red-400">*</span>
                            </span>
                        </label>
                        <input type="text" wire:model="location"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all"
                            placeholder="e.g. Jalan Setia 2, near Block A entrance">
                        @error('location') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Type + Severity + Status --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select wire:model="notice_type"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all text-sm">
                                @foreach ($noticeTypes as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Severity</label>
                            <select wire:model="severity"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all text-sm">
                                @foreach ($severities as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select wire:model="status"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all text-sm">
                                <option value="Active">Active</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea wire:model="description" rows="4"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all"
                            placeholder="Describe the obstruction. e.g. A kenduri tent has been erected on Jalan Setia 2, blocking both lanes. Residents should use the alternative route via Jalan Setia 4."></textarea>
                        @error('description') <span class="text-red-500 text-xs font-semibold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Start / End Times --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Start Time</label>
                            <input type="datetime-local" wire:model="starts_at"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all text-sm">
                            @error('starts_at') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">End Time <span class="text-xs font-normal text-gray-400">(optional)</span></label>
                            <input type="datetime-local" wire:model="ends_at"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-3.5 outline-none transition-all text-sm">
                            @error('ends_at') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Photo Upload --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Photo of Obstruction <span class="text-xs font-normal text-gray-400">(optional)</span>
                        </label>
                        <label class="flex flex-col items-center justify-center w-full h-28 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/30 cursor-pointer hover:border-amber-500 hover:bg-amber-50/5 transition-all group">
                            <div class="flex flex-col items-center gap-2 text-gray-400 group-hover:text-amber-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <span class="text-xs font-semibold">Click to upload photo</span>
                                <span class="text-xs text-gray-400">PNG, JPG up to 8MB</span>
                            </div>
                            <input wire:model="image" type="file" class="hidden" accept="image/*">
                        </label>
                        @if ($image)
                            <p class="mt-2 text-xs text-amber-500 font-semibold flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Photo attached: {{ is_object($image) ? $image->getClientOriginalName() : 'Image ready' }}
                            </p>
                        @endif
                        @error('image') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Notice preview --}}
                    @if (!$isEditing)
                        <div class="rounded-xl p-4 text-xs" style="background: rgba(245,158,11,0.07); border: 1px solid rgba(245,158,11,0.2);">
                            <p class="text-amber-400 font-bold mb-1">📢 Push Notification Preview</p>
                            <p class="text-gray-400">All residents will receive a notification when you submit this notice.</p>
                        </div>
                    @endif

                </div>

                {{-- Modal Footer --}}
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="closeModal"
                        class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors outline-none">
                        Cancel
                    </button>
                    <button wire:click="save"
                        class="w-full sm:w-auto px-8 py-3 font-bold rounded-xl text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 outline-none flex items-center justify-center gap-2"
                        style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,0.35);">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEditing ? 'Save Changes' : '📢 Post & Notify Residents' }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Posting...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
