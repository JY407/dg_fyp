<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Visitor;
use App\Models\User;
use App\Models\Announcement;
use App\Models\EmergencyAlert;
use App\Models\ForumPost;

new #[Layout('layouts.admin')] class extends Component {

    public function resolveAlert($id)
    {
        $alert = EmergencyAlert::find($id);
        if ($alert) {
            $alert->update(['status' => 'resolved']);
        }
        session()->flash('success', 'Alert resolved.');
    }

    public function with()
    {
        return [
            'stats' => [
                'total_users'         => User::where('user_type', '!=', 'admin')->count(),
                'total_announcements' => Announcement::count(),
                'total_forums'        => ForumPost::count(),
                'active_visitors'     => Visitor::whereNotNull('latitude')->count(),
            ],
            'recent_visitors'    => Visitor::latest()->take(5)->get(),
            'emergency_alerts'   => EmergencyAlert::with('user')->where('status', 'pending')->latest()->get(),
        ];
    }
}; ?>

@push('styles')
<style>
    /* ── Stat cards ── */
    .stat-card {
        background: rgba(30,41,59,.65);
        border: 1px solid rgba(148,163,184,.1);
        border-radius: 18px;
        padding: 1.4rem 1.6rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
        backdrop-filter: blur(12px);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 36px rgba(0,0,0,.35);
        border-color: rgba(99,102,241,.28);
    }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .stat-label {
        font-size: 0.78rem; font-weight: 600; color: #64748b;
        text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px;
    }
    .stat-value {
        font-size: 2.1rem; font-weight: 800; color: #f1f5f9; line-height: 1;
    }

    /* ── Table/list panels ── */
    .admin-panel {
        background: rgba(30,41,59,.6);
        border: 1px solid rgba(148,163,184,.08);
        border-radius: 18px;
        overflow: hidden;
        backdrop-filter: blur(12px);
    }
    .admin-panel-header {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid rgba(148,163,184,.08);
        display: flex; justify-content: space-between; align-items: center;
        background: rgba(15,23,42,.4);
    }
    .admin-panel-title {
        font-size: .9rem; font-weight: 700; color: #e2e8f0;
    }

    /* ── Emergency alert banner ── */
    .emergency-banner {
        border-radius: 18px; overflow: hidden; margin-bottom: 2rem;
        border: 1px solid rgba(239,68,68,.25);
        background: rgba(239,68,68,.07);
        backdrop-filter: blur(12px);
    }
    .emergency-banner-header {
        padding: .9rem 1.4rem;
        background: rgba(239,68,68,.18);
        display: flex; align-items: center; justify-content: space-between;
    }
    .emergency-alert-row {
        display: flex; align-items: center; justify-content: space-between;
        background: rgba(15,23,42,.6);
        border: 1px solid rgba(239,68,68,.15);
        border-radius: 12px;
        padding: .85rem 1.2rem;
    }
    .resolve-btn {
        padding: 6px 14px; border-radius: 8px;
        background: rgba(239,68,68,.18); border: 1px solid rgba(239,68,68,.3);
        color: #fca5a5; font-size: 12px; font-weight: 700; cursor: pointer;
        transition: background .18s, border-color .18s;
        font-family: inherit;
    }
    .resolve-btn:hover {
        background: rgba(239,68,68,.3); border-color: rgba(239,68,68,.5); color: #fff;
    }

    /* ── Quick nav links ── */
    .quick-nav-link {
        display: flex; align-items: center; justify-content: space-between;
        padding: .7rem 1rem; border-radius: 10px;
        border: 1px solid transparent;
        transition: all .18s ease; text-decoration: none;
        color: #64748b; font-size: .84rem; font-weight: 500;
    }
    .quick-nav-link:hover {
        background: rgba(99,102,241,.08);
        border-color: rgba(99,102,241,.18);
        color: #c7d2fe;
    }
    .quick-nav-link:hover svg { stroke: #818cf8; }
    .quick-nav-link svg { width: 15px; height: 15px; stroke: #334155; transition: stroke .18s; }
</style>
@endpush

<div>
    @if (session()->has('success'))
        <div style="margin-bottom:1.5rem; background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); color:#6ee7b7; padding:12px 18px; border-radius:12px; display:flex; align-items:center; gap:10px; font-size:14px; font-weight:500;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.5rem; font-weight:800; color:#f1f5f9; letter-spacing:-.025em; margin-bottom:4px;">Dashboard Overview</h1>
        <p style="font-size:13px; color:#475569; margin:0;">Welcome back, <strong style="color:#94a3b8;">{{ auth()->user()->name }}</strong> — here's what's happening today.</p>
    </div>

    {{-- Emergency Alerts --}}
    @if($emergency_alerts->isNotEmpty())
        <div class="emergency-banner">
            <div class="emergency-banner-header">
                <div style="display:flex; align-items:center; gap:10px; color:#fca5a5;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;animation:admin-status-pulse 1.5s infinite;"></span>
                    <span style="font-weight:800; font-size:.9rem; letter-spacing:.02em;">EMERGENCY ALERTS ACTIVE ({{ $emergency_alerts->count() }})</span>
                </div>
                <a href="{{ route('admin.emergencies-management') }}" style="color:#fca5a5; font-size:12px; font-weight:700; text-decoration:underline; opacity:.8;">View All →</a>
            </div>
            <div style="padding:1rem 1.2rem; display:flex; flex-direction:column; gap:.6rem;">
                @foreach($emergency_alerts as $alert)
                    <div class="emergency-alert-row">
                        <div>
                            <div style="font-weight:700; color:#f1f5f9; font-size:.85rem; margin-bottom:3px;">
                                {{ $alert->user->name }}
                                <span style="margin-left:6px; font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; background:rgba(239,68,68,.18); color:#fca5a5;">Unit {{ $alert->user->unit_number ?? 'N/A' }}</span>
                            </div>
                            <div style="font-size:12px; color:#475569;">{{ $alert->created_at->diffForHumans() }} &bull; {{ $alert->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <button wire:click="resolveAlert({{ $alert->id }})" wire:confirm="Resolve this emergency alert?" class="resolve-btn">Resolve</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        {{-- Total Users --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,.12); border:1px solid rgba(99,102,241,.2);">
                <svg class="w-6 h-6" style="color:#818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Users</p>
                <p class="stat-value">{{ $stats['total_users'] }}</p>
            </div>
        </div>

        {{-- Announcements --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.2);">
                <svg class="w-6 h-6" style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="stat-label">Announcements</p>
                <p class="stat-value">{{ $stats['total_announcements'] }}</p>
            </div>
        </div>

        {{-- Forum Posts --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(6,182,212,.12); border:1px solid rgba(6,182,212,.2);">
                <svg class="w-6 h-6" style="color:#22d3ee;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <p class="stat-label">Forum Posts</p>
                <p class="stat-value">{{ $stats['total_forums'] }}</p>
            </div>
        </div>

        {{-- Tracked Visitors --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.2);">
                <svg class="w-6 h-6" style="color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="stat-label">Tracked Visitors</p>
                <p class="stat-value">{{ $stats['active_visitors'] }}</p>
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Visitor Activity + Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Recent Visitor Activity --}}
        <div class="admin-panel lg:col-span-2">
            <div class="admin-panel-header">
                <h2 class="admin-panel-title">Recent Visitor Activity</h2>
                <a href="{{ route('admin.visitors.create') }}"
                   style="font-size:12px; font-weight:700; color:#818cf8; text-decoration:none; display:flex; align-items:center; gap:4px; padding:5px 12px; border-radius:8px; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2);">
                    + Record Visitor
                </a>
            </div>
            <ul>
                @forelse($recent_visitors as $visitor)
                    <li style="padding:.9rem 1.5rem; display:flex; align-items:center; gap:12px; border-bottom:1px solid rgba(148,163,184,.06);">
                        <div style="width:38px; height:38px; border-radius:10px; background:rgba(99,102,241,.12); border:1px solid rgba(99,102,241,.2); color:#818cf8; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.85rem;">
                            {{ strtoupper(substr($visitor->name, 0, 1)) }}
                        </div>
                        <div>
                            <p style="font-size:.85rem; font-weight:600; color:#e2e8f0; margin:0 0 2px;">{{ $visitor->name }}</p>
                            <p style="font-size:.75rem; color:#475569; margin:0;">Checked in {{ $visitor->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li style="padding:3rem 1.5rem; text-align:center; color:#334155; font-size:.85rem;">No recent visitor activity.</li>
                @endforelse
            </ul>
        </div>

        {{-- Quick Navigation --}}
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2 class="admin-panel-title">Quick Navigation</h2>
            </div>
            <div style="padding:.75rem;">
                @foreach([
                    ['route' => 'admin.announcements-management', 'label' => 'Announcements', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                    ['route' => 'admin.forum-management',         'label' => 'Forum',          'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                    ['route' => 'admin.emergencies-management',   'label' => 'Emergencies',    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ['route' => 'admin.facilities',               'label' => 'Facilities',     'icon' => 'M3 21h18M5 21V7l8-4 8 4v14M9 21v-4a2 2 0 012-2h2a2 2 0 012 2v4'],
                    ['route' => 'admin.events-management',        'label' => 'Events',         'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'admin.contact-messages',         'label' => 'Messages',       'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ] as $link)
                    <a href="{{ route($link['route']) }}" class="quick-nav-link">
                        <span style="display:flex; align-items:center; gap:9px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:#475569;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                            </svg>
                            {{ $link['label'] }}
                        </span>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>