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

<div>
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Welcome back, <strong class="text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</strong>. Here's what's happening.</p>
    </div>

    {{-- Emergency Alerts Banner --}}
    @if($emergency_alerts->isNotEmpty())
        <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 overflow-hidden">
            <div class="px-6 py-4 bg-red-500 flex items-center justify-between">
                <div class="flex items-center gap-3 text-white">
                    <span class="w-3 h-3 rounded-full bg-white animate-pulse inline-block"></span>
                    <span class="font-bold text-lg">EMERGENCY ALERTS ACTIVE ({{ $emergency_alerts->count() }})</span>
                </div>
                <a href="{{ route('admin.emergencies-management') }}" class="text-white/80 hover:text-white text-sm font-semibold underline">View All →</a>
            </div>
            <div class="p-5 space-y-3">
                @foreach($emergency_alerts as $alert)
                    <div class="flex items-center justify-between bg-white rounded-xl px-5 py-4 border border-red-100 shadow-sm">
                        <div>
                            <div class="font-bold text-gray-900">{{ $alert->user->name }}
                                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded bg-red-100 text-red-700">Unit {{ $alert->user->unit_number ?? 'N/A' }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-0.5">{{ $alert->created_at->diffForHumans() }} &bull; {{ $alert->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <button wire:click="resolveAlert({{ $alert->id }})" wire:confirm="Resolve this alert?"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            Resolve
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        {{-- Total Users --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $stats['total_users'] }}</p>
            </div>
        </div>

        {{-- Announcements --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Announcements</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $stats['total_announcements'] }}</p>
            </div>
        </div>

        {{-- Forum Posts --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Forum Posts</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $stats['total_forums'] }}</p>
            </div>
        </div>

        {{-- Active Visitors --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tracked Visitors</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $stats['active_visitors'] }}</p>
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Recent Visitors + Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Visitor Activity --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Recent Visitor Activity</h2>
                <a href="{{ route('admin.visitors.create') }}" class="text-indigo-500 hover:text-indigo-400 font-semibold text-sm">+ Record Visitor</a>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($recent_visitors as $visitor)
                    <li class="px-6 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($visitor->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $visitor->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Checked in {{ $visitor->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-gray-400 text-sm">No recent visitor activity.</li>
                @endforelse
            </ul>
        </div>

        {{-- Quick Navigation --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Quick Navigation</h2>
            </div>
            <div class="p-4 grid grid-cols-1 gap-2">
                @foreach([
                    ['route' => 'admin.announcements-management', 'label' => 'Announcements'],
                    ['route' => 'admin.forum-management', 'label' => 'Forum'],
                    ['route' => 'admin.emergencies-management', 'label' => 'Emergencies'],
                    ['route' => 'admin.facilities', 'label' => 'Facilities'],
                    ['route' => 'admin.events-management', 'label' => 'Events'],
                    ['route' => 'admin.contact-messages', 'label' => 'Messages'],
                ] as $link)
                    <a href="{{ route($link['route']) }}"
                        class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all group">
                        <span class="font-medium text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white text-sm">{{ $link['label'] }}</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>