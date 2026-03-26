<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Announcement;

new #[Layout('layouts.app')] class extends Component {
    public string $search = '';
    public ?int $selectedId = null;

    public function with()
    {
        $announcements = Announcement::query()
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%')
            )
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get();

        return [
            'announcements' => $announcements,
            'selectedAnn'   => $this->selectedId
                ? $announcements->firstWhere('id', $this->selectedId)
                : null,
        ];
    }

    public function openAnnouncement(int $id): void
    {
        $this->selectedId = $id;
    }

    public function closeModal(): void
    {
        $this->selectedId = null;
    }
}; ?>

<div class="min-h-screen" style="background:#0f172a;">
    @push('styles')
    <style>
        /* ── Hero ── */
        .ann-hero {
            position: relative;
            overflow: hidden;
            padding: 3.5rem 2rem 3rem;
            background: linear-gradient(135deg, rgba(79,70,229,.25) 0%, rgba(139,92,246,.12) 40%, rgba(15,23,42,0) 80%);
            border-bottom: 1px solid rgba(99,102,241,.2);
        }
        .ann-hero-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
            pointer-events: none;
        }

        .ann-search-wrap { position: relative; }
        .ann-search-wrap svg { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); }
        .ann-search {
            width: 100%; padding: 13px 20px 13px 46px;
            background: rgba(15,23,42,.85); border: 1px solid rgba(99,102,241,.25);
            border-radius: 16px; color: #e2e8f0; font-size: 14px; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .ann-search::placeholder { color: #475569; }
        .ann-search:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.18), 0 0 20px rgba(99,102,241,.1); }

        /* ── Card ── */
        .ann-card {
            position: relative;
            background: rgba(30,41,59,.6);
            border: 1px solid rgba(71,85,105,.35);
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
            display: flex;
            flex-direction: column;
        }
        .ann-card:hover {
            transform: translateY(-4px);
            background: rgba(30,41,59,.9);
            border-color: rgba(99,102,241,.45);
            box-shadow: 0 20px 50px rgba(0,0,0,.4), 0 0 0 1px rgba(99,102,241,.2);
        }
        .ann-card-bar { height: 3px; width: 100%; }
        .ann-card-body { padding: 1.4rem 1.5rem 1.2rem; flex: 1; display: flex; flex-direction: column; }
        .ann-card-meta { display: flex; align-items: center; gap: 8px; margin-bottom: .75rem; }
        .ann-badge-new {
            font-size: 9px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
            padding: 2px 8px; border-radius: 9999px;
            background: rgba(99,102,241,.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,.35);
            animation: pulse-badge 2s ease-in-out infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { opacity: 1; }
            50% { opacity: .65; }
        }
        .ann-card-date { font-size: 11px; font-weight: 600; color: #64748b; }
        .ann-card-title {
            font-size: 16px; font-weight: 800; color: #f1f5f9; line-height: 1.4;
            margin-bottom: .6rem; letter-spacing: -.01em;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .ann-card:hover .ann-card-title { color: #a5b4fc; }
        .ann-card-preview {
            font-size: 13px; color: #64748b; line-height: 1.6;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
            flex: 1;
        }
        .ann-card-footer {
            padding: .85rem 1.5rem;
            border-top: 1px solid rgba(71,85,105,.2);
            display: flex; align-items: center; justify-content: space-between;
        }
        .ann-read-btn {
            font-size: 12px; font-weight: 700; color: #818cf8;
            display: flex; align-items: center; gap: 4px; transition: gap .2s;
        }
        .ann-card:hover .ann-read-btn { gap: 7px; }

        /* ── Modal ── */
        .ann-modal-backdrop {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.75);
            backdrop-filter: blur(12px);
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
            animation: fade-in .18s ease;
        }
        .ann-modal {
            width: 100%; max-width: 700px; max-height: 90vh;
            background: #0f172a;
            border: 1px solid rgba(99,102,241,.3);
            border-radius: 24px;
            box-shadow: 0 40px 100px rgba(0,0,0,.7), 0 0 0 1px rgba(99,102,241,.15);
            overflow: hidden;
            display: flex; flex-direction: column;
            animation: slide-up .22s cubic-bezier(.22,1,.36,1);
        }
        .ann-modal-top { height: 4px; width: 100%; background: linear-gradient(90deg,#6366f1,#8b5cf6,#a855f7); flex-shrink: 0; }
        .ann-modal-header {
            padding: 1.5rem 2rem 1.25rem;
            border-bottom: 1px solid rgba(71,85,105,.25);
            background: rgba(30,41,59,.5);
            display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
            flex-shrink: 0;
        }
        .ann-modal-close {
            width: 34px; height: 34px; border-radius: 10px; border: 1px solid rgba(71,85,105,.4);
            background: rgba(71,85,105,.15); display: flex; align-items: center; justify-content: center;
            color: #64748b; cursor: pointer; transition: all .2s; flex-shrink: 0;
        }
        .ann-modal-close:hover { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.35); color: #f87171; }
        .ann-modal-body { padding: 2rem; overflow-y: auto; flex: 1; }
        @keyframes fade-in { from { opacity:0; } to { opacity:1; } }
        @keyframes slide-up { from { opacity:0; transform: translateY(20px) scale(.97); } to { opacity:1; transform: translateY(0) scale(1); } }

        /* ── Empty state ── */
        .ann-empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 5rem 2rem; text-align: center;
        }
    </style>
    @endpush

    {{-- ═══════════════════════════════════════════
         HERO HEADER
    ════════════════════════════════════════════ --}}
    <div class="ann-hero">
        {{-- Background orbs --}}
        <div class="ann-hero-orb" style="width:600px;height:600px;background:rgba(99,102,241,.14);top:-260px;right:-80px;"></div>
        <div class="ann-hero-orb" style="width:400px;height:400px;background:rgba(139,92,246,.12);bottom:-180px;left:-80px;"></div>
        <div class="ann-hero-orb" style="width:200px;height:200px;background:rgba(168,85,247,.1);top:40px;left:40%;"></div>

        <div class="max-w-5xl mx-auto">
            {{-- Title row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-7">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xl shadow-indigo-900/50"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight leading-none"
                            style="background:linear-gradient(90deg,#e0e7ff,#a5b4fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                            {{ __('app.announcements_title') }}
                        </h1>
                        <p class="text-sm text-slate-400 mt-1.5">{{ __('app.announcements_subtitle') }}</p>
                    </div>
                </div>

                {{-- Count badge --}}
                @if($announcements->count() > 0)
                <div class="flex items-center gap-2 px-4 py-2 rounded-2xl border"
                    style="background:rgba(99,102,241,.1); border-color:rgba(99,102,241,.25);">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse inline-block"></span>
                    <span class="text-sm font-bold text-indigo-300">{{ $announcements->count() }} {{ $announcements->count() === 1 ? 'Notice' : 'Notices' }}</span>
                </div>
                @endif
            </div>

            {{-- Search --}}
            <div class="ann-search-wrap mt-6">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="{{ __('app.search') }}"
                    class="ann-search">
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         CONTENT
    ════════════════════════════════════════════ --}}
    <div class="px-6 py-8 max-w-5xl mx-auto">

        @if($announcements->isEmpty())
            {{-- Empty state --}}
            <div class="ann-empty">
                <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-5 shadow-xl"
                    style="background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2);">
                    <svg class="w-9 h-9 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-200 mb-2">
                    {{ $search ? __('app.no_results') : __('app.announcements_none') }}
                </h3>
                <p class="text-sm text-slate-500 max-w-sm">
                    {{ $search ? 'Try a different keyword or clear the search.' : 'Check back soon for updates from management.' }}
                </p>
                @if($search)
                    <button wire:click="$set('search','')"
                        class="mt-5 px-5 py-2 rounded-xl text-sm font-bold text-indigo-300 border border-indigo-700/50 hover:bg-indigo-900/30 transition-colors">
                        Clear search
                    </button>
                @endif
            </div>

        @else
            {{-- ── Latest / Featured (first announcement, full width) ── --}}
            @php
                $first = $announcements->first();
                $rest  = $announcements->skip(1);
                $firstIsNew = $first->published_at->diffInDays(now()) <= 3;
            @endphp

            <div class="ann-card mb-5 w-full" wire:click="openAnnouncement({{ $first->id }})">
                <div class="ann-card-bar" style="background:linear-gradient(90deg,#6366f1,#8b5cf6,#a855f7);"></div>
                <div class="p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
                    {{-- Left: icon --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-lg"
                        style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.25);">
                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    {{-- Right: content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 bg-indigo-900/30 border border-indigo-800/40 px-2.5 py-0.5 rounded-full">
                                Latest
                            </span>
                            @if($firstIsNew)
                                <span class="ann-badge-new">New</span>
                            @endif
                            <span class="text-xs text-slate-500">{{ $first->published_at->format('d F Y') }}</span>
                            <span class="text-xs text-slate-600 ml-auto">{{ $first->published_at->diffForHumans() }}</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white mb-2 tracking-tight leading-snug"
                            style="transition: color .2s;">
                            {{ $first->title }}
                        </h2>
                        <p class="text-slate-400 text-sm leading-relaxed line-clamp-3">
                            {{ $first->content }}
                        </p>
                        <div class="mt-4 flex items-center gap-1.5 text-indigo-400 text-sm font-bold ann-read-btn">
                            Read Full Notice
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Grid of remaining cards ── --}}
            @if($rest->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($rest as $ann)
                        @php
                            $isNew = $ann->published_at->diffInDays(now()) <= 3;
                            $colors = [
                                0 => '#6366f1',
                                1 => '#8b5cf6',
                                2 => '#06b6d4',
                                3 => '#10b981',
                                4 => '#f59e0b',
                                5 => '#ec4899',
                            ];
                            $barColor = $colors[$loop->index % count($colors)];
                        @endphp
                        <div class="ann-card" wire:click="openAnnouncement({{ $ann->id }})">
                            <div class="ann-card-bar" style="background:{{ $barColor }};"></div>
                            <div class="ann-card-body">
                                <div class="ann-card-meta">
                                    @if($isNew)
                                        <span class="ann-badge-new">New</span>
                                    @endif
                                    <span class="ann-card-date">{{ $ann->published_at->format('d M Y') }}</span>
                                </div>
                                <div class="ann-card-title">{{ $ann->title }}</div>
                                <p class="ann-card-preview">{{ $ann->content }}</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="text-[11px] text-slate-600">{{ $ann->published_at->diffForHumans() }}</span>
                                <span class="ann-read-btn">
                                    Read
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- ═══════════════════════════════════════════
         DETAIL MODAL
    ════════════════════════════════════════════ --}}
    @if($selectedAnn)
        @php $isNew = $selectedAnn->published_at->diffInDays(now()) <= 3; @endphp
        <div class="ann-modal-backdrop" wire:click.self="closeModal">
            <div class="ann-modal">
                {{-- ── Top bar ── --}}
                <div class="ann-modal-top"></div>

                {{-- ── Header ── --}}
                <div class="ann-modal-header">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            @if($isNew)
                                <span class="ann-badge-new">New</span>
                            @endif
                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $selectedAnn->published_at->format('d F Y, h:i A') }}
                            </span>
                            <span class="text-xs text-slate-600">·</span>
                            <span class="text-xs text-slate-600">{{ $selectedAnn->published_at->diffForHumans() }}</span>
                        </div>
                        <h2 class="text-xl font-extrabold text-white leading-tight tracking-tight">
                            {{ $selectedAnn->title }}
                        </h2>
                    </div>
                    <button wire:click="closeModal" class="ann-modal-close" title="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- ── Body ── --}}
                <div class="ann-modal-body">
                    <div class="h-px mb-6" style="background:linear-gradient(90deg,rgba(99,102,241,.4),rgba(139,92,246,.2),transparent);"></div>
                    <p class="text-slate-300 text-base leading-8 whitespace-pre-wrap">{{ $selectedAnn->content }}</p>
                </div>

                {{-- ── Footer ── --}}
                <div class="px-8 py-4 border-t flex items-center justify-between"
                    style="border-color:rgba(71,85,105,.25); background:rgba(30,41,59,.4);">
                    <span class="text-xs text-slate-600">Official Community Notice</span>
                    <button wire:click="closeModal"
                        class="px-5 py-2 rounded-xl text-sm font-bold text-white transition-all"
                        style="background:rgba(99,102,241,.2); border:1px solid rgba(99,102,241,.3);"
                        onmouseover="this.style.background='rgba(99,102,241,.35)'"
                        onmouseout="this.style.background='rgba(99,102,241,.2)'">
                        {{ __('app.close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
