<div>
    <div style="padding-top: 100px; padding-bottom: 40px; height: 100vh; display: flex; flex-direction: column;">
        <div class="container" style="flex: 1; display: flex; flex-direction: column;">

            <!-- Header -->
            <div class="mb-4">
                <h1 class="mb-2">{{ __('Messages') }}</h1>
                <p class="text-secondary">{{ __('Chat with your neighbors and community groups.') }}</p>
            </div>

            <!-- Chat Container -->
            <div class="grid" style="grid-template-columns: 320px 1fr; gap: 1.5rem; flex: 1; min-height: 0;">

                <!-- Sidebar (Users & Groups) -->
                <div class="glass-card"
                    style="display: flex; flex-direction: column; padding: 0; overflow: hidden; height: 100%;">
                    <div class="p-4 border-b border-[rgba(255,255,255,0.1)] flex justify-between items-center">
                        <h3 class="text-lg font-semibold">{{ __('Conversations') }}</h3>
                        <button wire:click="$set('showCreateGroupModal', true)"
                            class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded transition">
                            + New Group
                        </button>
                    </div>

                    <div class="overflow-y-auto custom-scrollbar" style="flex: 1;">

                        <!-- Groups Section -->
                        <div class="p-3 text-xs font-bold text-secondary uppercase tracking-wider">{{ __('Groups') }}
                        </div>
                        <div class="flex flex-col gap-1 p-2">
                            @foreach ($groups as $group)
                                <div wire:click="selectGroup({{$group->id}})"
                                    class="p-3 rounded-lg cursor-pointer transition-all duration-200 flex items-center gap-3
                                             {{ $isGroupChat && $selectedGroup && $selectedGroup->id === $group->id ? 'bg-[rgba(102,126,234,0.2)] border border-[rgba(102,126,234,0.3)]' : 'hover:bg-[rgba(255,255,255,0.05)] border border-transparent' }}">

                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($group->name, 0, 2) }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-white truncate">{{$group->name}}</div>
                                        <div class="text-xs text-secondary truncate">{{ $group->members->count() }} members
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Users Section -->
                        <div class="p-3 text-xs font-bold text-secondary uppercase tracking-wider">
                            {{ __('Direct Messages') }}
                        </div>

                        <div class="flex flex-col gap-1 p-2">
                            @foreach ($users as $user)
                                <div wire:click="selectUser({{$user->id}})"
                                    class="p-3 rounded-lg cursor-pointer transition-all duration-200 flex items-center gap-3
                                             {{ !$isGroupChat && $selectedUser && $selectedUser->id === $user->id ? 'bg-[rgba(102,126,234,0.2)] border border-[rgba(102,126,234,0.3)]' : 'hover:bg-[rgba(255,255,255,0.05)] border border-transparent' }}">

                                    <!-- Avatar Placeholder -->
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-white truncate">{{$user->name}}</div>
                                        <div class="text-xs text-secondary truncate">
                                            {{ ucfirst($user->user_type ?? 'Resident') }} • Unit
                                            {{ $user->unit_number ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="glass-card"
                    style="display: flex; flex-direction: column; padding: 0; overflow: hidden; height: 100%;">

                    <!-- Chat Header -->
                    <div
                        class="p-4 border-b border-[rgba(255,255,255,0.1)] bg-[rgba(0,0,0,0.2)] flex items-center gap-3">
                        @if($selectedUser || $selectedGroup)
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br {{ $isGroupChat ? 'from-green-500 to-teal-600' : 'from-indigo-500 to-purple-600' }} flex items-center justify-center text-white font-bold">
                                {{ $isGroupChat ? substr($selectedGroup->name, 0, 2) : substr($selectedUser->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="font-bold text-white">
                                    {{ $isGroupChat ? $selectedGroup->name : $selectedUser->name }}
                                </div>
                                <div class="text-xs text-secondary">
                                    {{ $isGroupChat ? $selectedGroup->members->count() . ' members' : ucfirst($selectedUser->user_type ?? 'Resident') . ' • Unit ' . ($selectedUser->unit_number ?? 'N/A') }}
                                </div>
                            </div>
                        @else
                            <div class="text-white">Select a conversation</div>
                        @endif
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[rgba(0,0,0,0.1)]">
                        @foreach ($chatMessages as $message)
                                            <div class="flex {{$message->sender_id === auth()->id() ? 'justify-end' : 'justify-start'}}">
                                                <div class="max-w-[70%]">
                                                    @if($isGroupChat && $message->sender_id !== auth()->id())
                                                        <div class="text-xs text-secondary mb-1 ml-1">{{ $message->sender->name }}</div>
                                                    @endif
                                                    <div
                                                        class="px-5 py-3 rounded-2xl shadow-md text-sm leading-relaxed
                                                                                                {{$message->sender_id === auth()->id()
                            ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-br-none'
                            : 'bg-[rgba(255,255,255,0.1)] text-white border border-[rgba(255,255,255,0.1)] rounded-bl-none'}}">
                                                        {{$message->message}}
                                                    </div>
                                                </div>
                                            </div>
                        @endforeach

                        <div id="typing-indicator" class="text-xs text-secondary italic h-4"></div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 border-t border-[rgba(255,255,255,0.1)] bg-[rgba(0,0,0,0.2)]">
                        <form wire:submit="submit" class="flex items-center gap-3">
                            <input wire:model.live="newMessage" type="text"
                                class="flex-1 bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-full px-6 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                                placeholder="Type your message..." style="backdrop-filter: blur(5px);" />

                            <button type="submit"
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white flex items-center justify-center hover:scale-105 hover:shadow-lg transition-all duration-200 disabled:opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    @if($showCreateGroupModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="glass-card w-full max-w-md p-6 relative">
                <button wire:click="$set('showCreateGroupModal', false)"
                    class="absolute top-4 right-4 text-secondary hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <h2 class="text-xl font-bold mb-4">{{ __('Create New Group') }}</h2>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">{{ __('Group Name') }}</label>
                        <input wire:model="newGroupName" type="text" class="form-input w-full"
                            placeholder="e.g. Hiking Club">
                        @error('newGroupName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label mb-2 block">{{ __('Select Members') }}</label>
                        <div
                            class="max-h-48 overflow-y-auto custom-scrollbar border border-[rgba(255,255,255,0.1)] rounded-lg p-2">
                            @foreach($users as $user)
                                <label
                                    class="flex items-center gap-3 p-2 hover:bg-[rgba(255,255,255,0.05)] rounded cursor-pointer">
                                    <input wire:model="selectedUsersForGroup" type="checkbox" value="{{ $user->id }}"
                                        class="rounded bg-transparent border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                    <span>{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedUsersForGroup') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button wire:click="createGroup" class="btn btn-primary w-full">
                            {{ __('Create Group') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Custom Scrollbar for Chat */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('userTyping', (event) => {
                console.log(event);
                window.Echo.private(`chat.${event.selectedUserID}`).whisper("typing", {
                    userID: event.userID,
                    userName: event.userName
                });
            });

            window.Echo.private(`chat.{{ auth()->id() }}`).listenForWhisper('typing', (event) => {
                var t = document.getElementById("typing-indicator");
                if (t) {
                    t.innerText = `${event.userName} is Typing...`;
                    setTimeout(() => {
                        t.innerText = '';
                    }, 2000);
                }
            });

            // Auto-scroll to bottom of chat
            const chatContainer = document.querySelector('.custom-scrollbar');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
</div>