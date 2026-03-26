<?php

use Livewire\Volt\Component;
use App\Models\Announcement;
use App\Models\UserNotification;

new class extends Component {
    public bool $open = false;

    public function getUnreadCountProperty(): int
    {
        if (!auth()->check()) return 0;
        $user = auth()->user();

        // Unread personal notifications
        $personal = UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // New announcements since user last read them
        $readAt = $user->notifications_read_at;
        $announcements = Announcement::where('published_at', '<=', now())
            ->when($readAt, fn($q) => $q->where('published_at', '>', $readAt))
            ->count();

        return $personal + $announcements;
    }

    public function getItemsProperty(): array
    {
        $user = auth()->user();
        $items = collect();

        // Personal notifications (unread first, then recent)
        $personal = UserNotification::where('user_id', $user->id)
            ->orderByRaw('read_at IS NOT NULL')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(fn($n) => [
                'type'    => $n->type,
                'title'   => $n->title,
                'body'    => $n->message,
                'time'    => $n->created_at->diffForHumans(),
                'isNew'   => is_null($n->read_at),
                'color'   => in_array($n->type, ['account_approved']) ? '#10b981' : '#ef4444',
                'iconType'=> 'user',
            ]);
        $items = $items->merge($personal);

        // Latest announcements
        $readAt = $user->notifications_read_at;
        $anns = Announcement::where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($a) => [
                'type'    => 'announcement',
                'title'   => $a->title,
                'body'    => $a->content,
                'time'    => $a->published_at->diffForHumans(),
                'isNew'   => $readAt ? $a->published_at->gt($readAt) : true,
                'color'   => '#6366f1',
                'iconType'=> 'bell',
            ]);
        $items = $items->merge($anns);

        return $items->sortByDesc('isNew')->values()->take(6)->all();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open && auth()->check()) {
            // Mark all personal notifications as read
            UserNotification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            // Mark announcements read timestamp
            auth()->user()->update(['notifications_read_at' => now()]);
        }
    }

    public function close(): void
    {
        $this->open = false;
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
                    @if($item['type'] === 'announcement')
                        <a href="{{ route('announcements') }}" class="flex items-start gap-3 px-4 py-3 transition-colors hover:bg-white/5">
                    @else
                        <div class="flex items-start gap-3 px-4 py-3 {{ $item['isNew'] ? 'bg-white/3' : '' }}">
                    @endif
                        {{-- Icon --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                            style="background:{{ $item['color'] }}18; border:1px solid {{ $item['color'] }}35;">
                            @if($item['iconType'] === 'user')
                                <svg class="w-4 h-4" style="color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4" style="color:{{ $item['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
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
                    @if($item['type'] === 'announcement')
                        </a>
                    @else
                        </div>
                    @endif
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
