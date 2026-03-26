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

<div style="background:#0f172a; min-height:100vh;">
    @push('styles')
    <style>
        /* ── Page wrapper ── */
        .ann-page { padding: 2rem; max-width: 1200px; margin: 0 auto; }

        /* ── Page header bar ── */
        .ann-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .ann-header-icon {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(99,102,241,.35);
        }
        .ann-header-text h1 {
            font-size: 20px; font-weight: 800; color: #f1f5f9;
            letter-spacing: -.02em; line-height: 1;
        }
        .ann-header-text p { font-size: 12px; color: #64748b; margin-top: 2px; }
        .ann-count-badge {
            margin-left: auto;
            display: flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 10px;
            background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.25);
        }
        .ann-count-badge span:first-child {
            width: 6px; height: 6px; border-radius: 50%;
            background: #818cf8; animation: pulse-dot 2s ease-in-out infinite;
        }
        .ann-count-badge span:last-child { font-size: 12px; font-weight: 700; color: #a5b4fc; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }

        /* ── Search bar ── */
        .ann-search-wrap { position: relative; margin-bottom: 1.75rem; }
        .ann-search-wrap .search-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #475569; pointer-events: none;
        }
        .ann-search {
            width: 100%; padding: 11px 16px 11px 42px;
            background: rgba(30,41,59,.7); border: 1px solid rgba(71,85,105,.4);
            border-radius: 12px; color: #e2e8f0; font-size: 13.5px; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .ann-search::placeholder { color: #475569; }
        .ann-search:focus {
            border-color: rgba(99,102,241,.6);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        /* ── Featured (top) card ── */
        .ann-featured {
            position: relative; border-radius: 18px; overflow: hidden;
            background: rgba(30,41,59,.7); border: 1px solid rgba(99,102,241,.2);
            margin-bottom: 1.5rem; cursor: pointer;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .ann-featured:hover {
            transform: translateY(-2px);
            border-color: rgba(99,102,241,.45);
            box-shadow: 0 16px 45px rgba(0,0,0,.4);
        }
        .ann-featured-bar {
            height: 3px;
            background: linear-gradient(90deg,#6366f1,#8b5cf6,#a855f7);
        }
        .ann-featured-body {
            padding: 1.4rem 1.6rem;
            display: flex; align-items: flex-start; gap: 1.2rem;
        }
        .ann-featured-icon {
            width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
            background: rgba(99,102,241,.15); border: 1px solid rgba(99,102,241,.25);
            display: flex; align-items: center; justify-content: center;
        }
        .ann-featured-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
        .ann-tag-latest {
            font-size: 10px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase;
            padding: 2px 8px; border-radius: 6px;
            background: rgba(99,102,241,.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,.3);
        }
        .ann-tag-new {
            font-size: 9px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
            padding: 2px 8px; border-radius: 6px;
            background: rgba(16,185,129,.15); color: #6ee7b7; border: 1px solid rgba(16,185,129,.3);
            animation: pulse-badge 2s ease-in-out infinite;
        }
        @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:.6} }
        .ann-featured-date { font-size: 11px; color: #64748b; margin-left: auto; }
        .ann-featured-title {
            font-size: 18px; font-weight: 800; color: #f1f5f9;
            letter-spacing: -.02em; line-height: 1.35; margin-bottom: 6px;
            transition: color .2s;
        }
        .ann-featured:hover .ann-featured-title { color: #a5b4fc; }
        .ann-featured-preview { font-size: 13px; color: #64748b; line-height: 1.65; }
        .ann-featured-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: .9rem 1.6rem; border-top: 1px solid rgba(71,85,105,.2);
        }
        .ann-read-link {
            font-size: 12px; font-weight: 700; color: #818cf8;
            display: flex; align-items: center; gap: 4px; transition: gap .2s;
        }
        .ann-featured:hover .ann-read-link { gap: 8px; }

        /* ── Card grid ── */
        .ann-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        @media(max-width:900px) { .ann-grid { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:580px) { .ann-grid { grid-template-columns: 1fr; } }

        .ann-card {
            border-radius: 16px; overflow: hidden;
            background: rgba(30,41,59,.55); border: 1px solid rgba(71,85,105,.3);
            cursor: pointer; display: flex; flex-direction: column;
            transition: transform .2s, box-shadow .2s, border-color .2s, background .2s;
        }
        .ann-card:hover {
            transform: translateY(-3px);
            background: rgba(30,41,59,.85);
            border-color: rgba(99,102,241,.4);
            box-shadow: 0 14px 40px rgba(0,0,0,.35);
        }
        .ann-card-bar { height: 3px; }
        .ann-card-body { padding: 1.1rem 1.2rem; flex: 1; }
        .ann-card-meta { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
        .ann-card-date { font-size: 10.5px; font-weight: 600; color: #475569; }
        .ann-card-title {
            font-size: 15px; font-weight: 800; color: #e2e8f0; line-height: 1.4;
            margin-bottom: 6px; letter-spacing: -.01em;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
            transition: color .2s;
        }
        .ann-card:hover .ann-card-title { color: #a5b4fc; }
        .ann-card-preview {
            font-size: 12.5px; color: #475569; line-height: 1.6;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        }
        .ann-card-footer {
            padding: .7rem 1.2rem; border-top: 1px solid rgba(71,85,105,.18);
            display: flex; align-items: center; justify-content: space-between;
        }
        .ann-card-time { font-size: 10px; color: #334155; }
        .ann-card-cta {
            font-size: 11px; font-weight: 700; color: #818cf8;
            display: flex; align-items: center; gap: 3px; transition: gap .2s;
        }
        .ann-card:hover .ann-card-cta { gap: 6px; }

        /* ── Empty state ── */
        .ann-empty {
            text-align: center; padding: 5rem 2rem;
            display: flex; flex-direction: column; align-items: center;
        }

        /* ── Modal ── */
        .ann-modal-backdrop {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.72); backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
            animation: fade-in .16s ease;
        }
        .ann-modal {
            width: 100%; max-width: 680px; max-height: 88vh;
            background: #0f172a; border: 1px solid rgba(99,102,241,.28);
            border-radius: 22px; box-shadow: 0 40px 100px rgba(0,0,0,.7);
            overflow: hidden; display: flex; flex-direction: column;
            animation: slide-up .2s cubic-bezier(.22,1,.36,1);
        }
        .ann-modal-bar { height: 3px; background: linear-gradient(90deg,#6366f1,#8b5cf6,#a855f7); flex-shrink:0; }
        .ann-modal-header {
            padding: 1.4rem 1.8rem 1.2rem;
            border-bottom: 1px solid rgba(71,85,105,.2);
            background: rgba(30,41,59,.45);
            display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
            flex-shrink: 0;
        }
        .ann-modal-close {
            width: 32px; height: 32px; border-radius: 9px;
            border: 1px solid rgba(71,85,105,.4); background: rgba(71,85,105,.15);
            display: flex; align-items: center; justify-content: center;
            color: #64748b; cursor: pointer; transition: all .2s; flex-shrink: 0;
        }
        .ann-modal-close:hover { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.3); color: #f87171; }
        .ann-modal-body { padding: 1.8rem; overflow-y: auto; flex: 1; }
        .ann-modal-footer {
            padding: .85rem 1.8rem; border-top: 1px solid rgba(71,85,105,.2);
            background: rgba(30,41,59,.35);
            display: flex; align-items: center; justify-content: space-between;
        }
        @keyframes fade-in { from{opacity:0} to{opacity:1} }
        @keyframes slide-up { from{opacity:0;transform:translateY(18px) scale(.97)} to{opacity:1;transform:none} }
    </style>
    @endpush

    <div class="ann-page">

        {{-- ── Header bar ── --}}
        <div class="ann-header">
            <div class="ann-header-icon">
                <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div class="ann-header-text">
                <h1>{{ __('app.announcements_title') }}</h1>
                <p>{{ __('app.announcements_subtitle') }}</p>
            </div>
            @if($announcements->count() > 0)
            <div class="ann-count-badge">
                <span></span>
                <span>{{ $announcements->count() }} {{ $announcements->count() === 1 ? 'Notice' : 'Notices' }}</span>
            </div>
            @endif
        </div>

        {{-- ── Search ── --}}
        <div class="ann-search-wrap">
            <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="{{ __('app.search') }}…"
                class="ann-search">
        </div>

        {{-- ── Content ── --}}
        @if($announcements->isEmpty())
            <div class="ann-empty">
                <div style="width:64px;height:64px;border-radius:16px;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;margin-bottom:1.2rem;">
                    <svg width="28" height="28" fill="none" stroke="#818cf8" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 style="font-size:17px;font-weight:800;color:#e2e8f0;margin-bottom:6px;">
                    {{ $search ? __('app.no_results') : __('app.announcements_none') }}
                </h3>
                <p style="font-size:13px;color:#475569;max-width:320px;">
                    {{ $search ? 'Try a different keyword.' : 'Check back soon for updates from management.' }}
                </p>
                @if($search)
                    <button wire:click="$set('search','')"
                        style="margin-top:1rem;padding:7px 18px;border-radius:10px;font-size:12px;font-weight:700;color:#a5b4fc;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.25);cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(99,102,241,.22)'"
                        onmouseout="this.style.background='rgba(99,102,241,.12)'">
                        Clear search
                    </button>
                @endif
            </div>

        @else
            @php
                $first = $announcements->first();
                $rest  = $announcements->skip(1);
                $firstIsNew = $first->published_at->diffInDays(now()) <= 3;
                $barColors  = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ec4899'];
            @endphp

            {{-- ── Featured card ── --}}
            <div class="ann-featured" wire:click="openAnnouncement({{ $first->id }})">
                <div class="ann-featured-bar"></div>
                <div class="ann-featured-body">
                    <div class="ann-featured-icon">
                        <svg width="20" height="20" fill="none" stroke="#818cf8" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="ann-featured-meta">
                            <span class="ann-tag-latest">Latest</span>
                            @if($firstIsNew)<span class="ann-tag-new">New</span>@endif
                            <span class="ann-featured-date">{{ $first->published_at->format('d F Y') }} · {{ $first->published_at->diffForHumans() }}</span>
                        </div>
                        <div class="ann-featured-title">{{ $first->title }}</div>
                        <p class="ann-featured-preview">{{ Str::limit($first->content, 160) }}</p>
                    </div>
                </div>
                <div class="ann-featured-footer">
                    <span style="font-size:11px;color:#334155;">Official Notice</span>
                    <span class="ann-read-link">
                        Read Full Notice
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- ── Card grid ── --}}
            @if($rest->count() > 0)
            <div class="ann-grid">
                @foreach($rest as $ann)
                    @php
                        $isNew    = $ann->published_at->diffInDays(now()) <= 3;
                        $barColor = $barColors[$loop->index % count($barColors)];
                    @endphp
                    <div class="ann-card" wire:click="openAnnouncement({{ $ann->id }})">
                        <div class="ann-card-bar" style="background:{{ $barColor }};"></div>
                        <div class="ann-card-body">
                            <div class="ann-card-meta">
                                @if($isNew)<span class="ann-tag-new">New</span>@endif
                                <span class="ann-card-date">{{ $ann->published_at->format('d M Y') }}</span>
                            </div>
                            <div class="ann-card-title">{{ $ann->title }}</div>
                            <p class="ann-card-preview">{{ $ann->content }}</p>
                        </div>
                        <div class="ann-card-footer">
                            <span class="ann-card-time">{{ $ann->published_at->diffForHumans() }}</span>
                            <span class="ann-card-cta">
                                Read
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        @endif

    </div>{{-- /ann-page --}}

    {{-- ── Detail Modal ── --}}
    @if($selectedAnn)
        @php $isNew = $selectedAnn->published_at->diffInDays(now()) <= 3; @endphp
        <div class="ann-modal-backdrop" wire:click.self="closeModal">
            <div class="ann-modal">
                <div class="ann-modal-bar"></div>
                <div class="ann-modal-header">
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                            @if($isNew)<span class="ann-tag-new">New</span>@endif
                            <span style="font-size:11px;color:#64748b;">
                                {{ $selectedAnn->published_at->format('d F Y, h:i A') }}
                                · {{ $selectedAnn->published_at->diffForHumans() }}
                            </span>
                        </div>
                        <h2 style="font-size:19px;font-weight:800;color:#f1f5f9;letter-spacing:-.02em;line-height:1.3;">
                            {{ $selectedAnn->title }}
                        </h2>
                    </div>
                    <button wire:click="closeModal" class="ann-modal-close" title="Close">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="ann-modal-body">
                    <div style="height:1px;background:linear-gradient(90deg,rgba(99,102,241,.45),rgba(139,92,246,.2),transparent);margin-bottom:1.4rem;"></div>
                    <p style="color:#cbd5e1;font-size:14.5px;line-height:1.85;white-space:pre-wrap;">{{ $selectedAnn->content }}</p>
                </div>
                <div class="ann-modal-footer">
                    <span style="font-size:11px;color:#334155;">Official Community Notice</span>
                    <button wire:click="closeModal"
                        style="padding:7px 18px;border-radius:10px;font-size:12px;font-weight:700;color:#a5b4fc;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(99,102,241,.28)'"
                        onmouseout="this.style.background='rgba(99,102,241,.15)'">
                        {{ __('app.close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
