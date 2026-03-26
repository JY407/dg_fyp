<?php

use Livewire\Volt\Component;
use App\Models\UserNotification;

new class extends Component {
    public bool $open = false;

    // ── Computed: unread count ──────────────────────────────────────────────
    public function getUnreadCountProperty(): int
    {
        if (!auth()->check()) return 0;
        return UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    // ── Computed: items list ────────────────────────────────────────────────
    public function getItemsProperty(): array
    {
        if (!auth()->check()) return [];

        return UserNotification::where('user_id', auth()->id())
            ->orderByRaw('read_at IS NOT NULL')   // unread first
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn($n) => [
                'type'     => $n->type,
                'title'    => $n->title,
                'body'     => $n->message,
                'time'     => $n->created_at->diffForHumans(),
                'isNew'    => is_null($n->read_at),
                'color'    => $this->colorFor($n->type),
                'iconType' => $this->iconFor($n->type),
            ])
            ->all();
    }

    // ── Toggle open / close ─────────────────────────────────────────────────
    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open && auth()->check()) {
            UserNotification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function close(): void
    {
        $this->open = false;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────
    private function colorFor(string $type): string
    {
        return match($type) {
            'announcement'   => '#6366f1',   // indigo
            'event_new'      => '#10b981',   // emerald
            'event_updated'  => '#f59e0b',   // amber
            'event_approved' => '#10b981',   // emerald
            'account_approved' => '#10b981',
            default          => '#8b5cf6',   // purple
        };
    }

    private function iconFor(string $type): string
    {
        return match($type) {
            'announcement'               => 'megaphone',
            'event_new', 'event_updated',
            'event_approved'             => 'calendar',
            default                      => 'user',
        };
    }
}; ?>

<div class="relative" x-data="{ open: @entangle('open') }" @click.outside="$wire.close()">
    {{-- Bell Button --}}
    <button @click="$wire.toggle()"
        class="relative flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-200 hover:bg-white/10 focus:outline-none"
        title="Notifications">
        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] rounded-full flex items-center justify-center text-[9px] font-extrabold text-white"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6); padding:0 4px; animation: pulse 1.5s infinite;">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 rounded-2xl border border-slate-700/60 shadow-2xl overflow-hidden z-50"
        style="background:#1e293b; top:100%;" wire:ignore.self>

        {{-- Header --}}
        <div class="px-4 py-3 flex items-center justify-between border-b border-slate-700/50"
            style="background:rgba(15,23,42,.8);">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="text-sm font-bold text-white">Notifications</span>
                @if($this->unreadCount > 0)
                    <span class="text-[10px] font-bold text-indigo-300 bg-indigo-900/50 px-1.5 py-0.5 rounded-full">
                        {{ $this->unreadCount }} new
                    </span>
                @endif
            </div>
            <a href="{{ route('announcements') }}"
                class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                View all →
            </a>
        </div>

        {{-- Items --}}
        <ul class="max-h-80 overflow-y-auto divide-y divide-slate-700/30">
            @forelse($this->items as $item)
                <li>
                    @php
                        $isEventType = in_array($item['type'], ['event_new','event_updated','event_approved']);
                        $linkTarget  = $isEventType ? route('events') : route('announcements');
                    @endphp
                    <a href="{{ $linkTarget }}"
                        class="flex items-start gap-3 px-4 py-3 transition-colors hover:bg-white/5 {{ $item['isNew'] ? 'bg-white/[0.03]' : '' }}">

                        {{-- Icon --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                            style="background:{{ $item['color'] }}18; border:1px solid {{ $item['color'] }}35;">
                            @if($item['iconType'] === 'calendar')
                                {{-- Calendar icon for events --}}
                                <svg class="w-4 h-4" style="color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @elseif($item['iconType'] === 'megaphone')
                                {{-- Megaphone icon for announcements --}}
                                <svg class="w-4 h-4" style="color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            @else
                                {{-- User icon for account notifications --}}
                                <svg class="w-4 h-4" style="color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-semibold text-slate-200 line-clamp-1">{{ $item['title'] }}</p>
                                @if($item['isNew'])
                                    <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $item['color'] }};"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">{{ $item['body'] }}</p>
                            <p class="text-[10px] text-slate-600 mt-1">{{ $item['time'] }}</p>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-10 text-center">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-sm text-slate-500">No notifications yet.</p>
                </li>
            @endforelse
        </ul>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-slate-700/50 text-center" style="background:rgba(15,23,42,.6);">
            <a href="{{ route('announcements') }}"
                class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                See all announcements →
            </a>
        </div>
    </div>
</div>
