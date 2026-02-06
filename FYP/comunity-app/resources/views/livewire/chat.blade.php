<div x-data="{ showProfileInfo: false }">
    <div style="padding-top: 100px; padding-bottom: 40px; height: 100vh; display: flex; flex-direction: column;">
        <div class="container" style="flex: 1; display: flex; flex-direction: column; max-width: 100%;">

            <!-- Header -->
            <div class="mb-4">
                <h1 class="mb-2">{{ __('Messages') }}</h1>
                <p class="text-secondary">{{ __('Chat with your neighbors and community groups.') }}</p>
            </div>

            <!-- Chat Container (Vertical Stack) -->
            <div class="flex flex-col gap-4" style="flex: 1; min-height: 0;">

                <!-- Top Bar: Conversations (Horizontal Scroll) -->
                <div class="glass-card w-full" style="padding: 1rem; flex-shrink: 0;">
                    <div class="flex items-center gap-4 overflow-x-auto custom-scrollbar pb-2">
                        
                        <!-- New Group Button -->
                        <button wire:click="$set('showCreateGroupModal', true)"
                            class="flex flex-col items-center gap-2 min-w-[70px] group cursor-pointer">
                            <div class="w-14 h-14 rounded-full bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </div>
                            <span class="text-xs font-medium text-secondary group-hover:text-white truncate max-w-full">New Group</span>
                        </button>

                        <div class="w-[1px] h-10 bg-white/10 mx-2"></div>

                        <!-- Groups -->
                        @foreach ($groups as $group)
                            <div wire:click="selectGroup({{$group->id}})"
                                class="flex flex-col items-center gap-2 min-w-[70px] cursor-pointer group">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-lg relative transition-all duration-300
                                    {{ $isGroupChat && $selectedGroup && $selectedGroup->id === $group->id 
                                        ? 'bg-gradient-to-br from-green-500 to-teal-600 ring-2 ring-green-400 ring-offset-2 ring-offset-black' 
                                        : 'bg-gradient-to-br from-gray-700 to-gray-600 opacity-70 hover:opacity-100 hover:scale-110' 
                                    }}">
                                    {{ substr($group->name, 0, 2) }}
                                    @if($isGroupChat && $selectedGroup && $selectedGroup->id === $group->id)
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-black"></div>
                                    @endif
                                </div>
                                <span class="text-xs font-medium text-center truncate w-20 {{ $isGroupChat && $selectedGroup && $selectedGroup->id === $group->id ? 'text-green-400' : 'text-secondary group-hover:text-white' }}">
                                    {{$group->name}}
                                </span>
                            </div>
                        @endforeach

                        @if($groups->count() > 0 && $users->count() > 0)
                            <div class="w-[1px] h-10 bg-white/10 mx-2"></div>
                        @endif

                        <!-- Users -->
                        @foreach ($users as $user)
                            <div wire:click="selectUser({{$user->id}})"
                                class="flex flex-col items-center gap-2 min-w-[70px] cursor-pointer group">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-lg relative transition-all duration-300
                                    {{ !$isGroupChat && $selectedUser && $selectedUser->id === $user->id 
                                        ? 'bg-gradient-to-br from-indigo-500 to-purple-600 ring-2 ring-indigo-400 ring-offset-2 ring-offset-black' 
                                        : 'bg-gradient-to-br from-gray-700 to-gray-600 opacity-70 hover:opacity-100 hover:scale-110' 
                                    }}">
                                    {{ substr($user->name, 0, 2) }}
                                    @if(!$isGroupChat && $selectedUser && $selectedUser->id === $user->id)
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-indigo-500 rounded-full border-2 border-black"></div>
                                    @endif
                                </div>
                                <span class="text-xs font-medium text-center truncate w-20 {{ !$isGroupChat && $selectedUser && $selectedUser->id === $user->id ? 'text-indigo-400' : 'text-secondary group-hover:text-white' }}">
                                    {{$user->name}}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="glass-card flex-1 flex flex-col overflow-hidden" style="padding: 0;">
                    
                    <!-- Chat Header -->
                    <div @click="showProfileInfo = true" 
                        class="p-4 border-b border-[rgba(255,255,255,0.1)] bg-[rgba(0,0,0,0.2)] flex items-center gap-3 cursor-pointer hover:bg-white/5 transition-colors group relative">
                        
                        @if($selectedUser || $selectedGroup)
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $isGroupChat ? 'from-green-500 to-teal-600' : 'from-indigo-500 to-purple-600' }} flex items-center justify-center text-white font-bold animate-fade-in">
                                {{ $isGroupChat ? substr($selectedGroup->name, 0, 2) : substr($selectedUser->name, 0, 2) }}
                            </div>
                            <div class="animate-fade-in-up flex-1">
                                <div class="font-bold text-white text-lg group-hover:text-indigo-300 transition-colors flex items-center gap-2">
                                    {{ $isGroupChat ? $selectedGroup->name : $selectedUser->name }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-hover:opacity-100 transition-opacity text-secondary"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                </div>
                                <div class="text-xs text-secondary flex items-center gap-2">
                                    @if($isGroupChat)
                                        <span class="bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider">Group</span>
                                        <span>{{ $selectedGroup->members->count() }} members</span>
                                    @else
                                        <span class="bg-indigo-500/20 text-indigo-400 px-1.5 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider">Direct</span>
                                        <span>{{ ucfirst($selectedUser->user_type ?? 'Resident') }} • Unit {{ $selectedUser->unit_number ?? 'N/A' }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-white text-lg animate-pulse">Select a conversation from the bar above to start chatting</div>
                        @endif
                    </div>



                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[rgba(0,0,0,0.1)]">
                        @foreach ($chatMessages as $message)
                            <div class="flex {{$message->sender_id === auth()->id() ? 'justify-end' : 'justify-start'}} animate-fade-in-up">
                                <div class="max-w-[70%]">
                                    @if($isGroupChat && $message->sender_id !== auth()->id())
                                        <div class="text-xs text-secondary mb-1 ml-1">{{ $message->sender->name }}</div>
                                    @endif
                                    <div class="px-5 py-3 rounded-2xl shadow-md text-sm leading-relaxed
                                        {{$message->sender_id === auth()->id()
                                            ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-br-none'
                                            : 'bg-[rgba(255,255,255,0.1)] text-white border border-[rgba(255,255,255,0.1)] rounded-bl-none'}}">
                                        {{$message->message}}
                                    </div>
                                    <div class="text-[10px] text-white/30 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right mr-1' : 'ml-1' }}">
                                        {{ $message->created_at->format('h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div id="typing-indicator" class="text-xs text-secondary italic h-4 pl-2"></div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 border-t border-[rgba(255,255,255,0.1)] bg-[rgba(0,0,0,0.2)]">
                        <form wire:submit="submit" class="flex items-center gap-3">
                            <input wire:model.live="newMessage" type="text"
                                class="flex-1 bg-[rgba(255,255,255,0.05)] border border-[rgba(255,255,255,0.1)] rounded-full px-6 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                                placeholder="Type your message..." style="backdrop-filter: blur(5px);" 
                                {{ (!$selectedUser && !$selectedGroup) ? 'disabled' : '' }} />

                            <button type="submit"
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white flex items-center justify-center hover:scale-105 hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ (!$selectedUser && !$selectedGroup) ? 'disabled' : '' }}>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm animate-fade-in">
            <div class="glass-card w-full max-w-md p-6 relative animate-scale-in">
                <button wire:click="$set('showCreateGroupModal', false)"
                    class="absolute top-4 right-4 text-secondary hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold">{{ __('Create New Group') }}</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">{{ __('Group Name') }}</label>
                        <input wire:model="newGroupName" type="text" class="form-input w-full"
                            placeholder="e.g. Hiking Club">
                        @error('newGroupName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label mb-2 block">{{ __('Select Members') }}</label>
                        <div
                            class="max-h-48 overflow-y-auto custom-scrollbar border border-[rgba(255,255,255,0.1)] rounded-lg p-2 bg-black/10">
                            @foreach($users as $user)
                                <label
                                    class="flex items-center gap-3 p-2 hover:bg-[rgba(255,255,255,0.05)] rounded cursor-pointer transition-colors group">
                                    <div class="relative flex items-center">
                                        <input wire:model="selectedUsersForGroup" type="checkbox" value="{{ $user->id }}"
                                            class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-gray-500 transition-all checked:border-indigo-500 checked:bg-indigo-500">
                                        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </div>
                                    <span class="text-sm text-secondary group-hover:text-white transition-colors">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedUsersForGroup') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button wire:click="createGroup" class="btn btn-primary w-full py-3">
                            {{ __('Create Group') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Info Modal (Moved to Root) -->
    <div x-show="showProfileInfo" 
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        
        <div @click.away="showProfileInfo = false" class="glass-card w-full max-w-sm p-6 relative flex flex-col items-center text-center animate-scale-in">
            <button @click.stop="showProfileInfo = false" class="absolute top-4 right-4 text-secondary hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>

            @if($selectedUser || $selectedGroup)
                <div class="w-24 h-24 rounded-full bg-gradient-to-br {{ $isGroupChat ? 'from-green-500 to-teal-600' : 'from-indigo-500 to-purple-600' }} flex items-center justify-center text-white font-bold text-3xl mb-4 shadow-lg ring-4 ring-white/10">
                    {{ $isGroupChat ? substr($selectedGroup->name, 0, 2) : substr($selectedUser->name, 0, 2) }}
                </div>
                
                <h2 class="text-2xl font-bold text-white mb-1">
                    {{ $isGroupChat ? $selectedGroup->name : $selectedUser->name }}
                </h2>
                
                @if(!$isGroupChat)
                    <div class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                        {{ ucfirst($selectedUser->user_type ?? 'Resident') }}
                    </div>
                    
                    <div class="w-full space-y-3 text-left">
                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </div>
                            <div>
                                <div class="text-xs text-secondary">Unit Number</div>
                                <div class="text-sm font-medium text-white">{{ $selectedUser->unit_number ?? 'Not assigned' }}</div>
                            </div>
                        </div>
                        
                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <div>
                                <div class="text-xs text-secondary">Email Address</div>
                                <div class="text-sm font-medium text-white">{{ $selectedUser->email ?? 'Hidden' }}</div>
                            </div>
                        </div>

                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div>
                                <div class="text-xs text-secondary">Joined</div>
                                <div class="text-sm font-medium text-white">{{ $selectedUser->created_at ? $selectedUser->created_at->format('M d, Y') : 'Unknown' }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                        Group Chat
                    </div>
                    
                    <div class="w-full bg-black/20 rounded-xl p-4 text-left border border-white/5">
                        <h3 class="text-xs font-bold text-secondary uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>Members</span>
                            <span class="bg-white/10 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $selectedGroup->members->count() }}</span>
                        </h3>
                        <div class="max-h-48 overflow-y-auto custom-scrollbar space-y-2 pr-1">
                            @foreach($selectedGroup->members as $member)
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-700 to-gray-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ substr($member->user->name, 0, 2) }}
                                    </div>
                                    <div class="text-sm text-white font-medium">{{ $member->user->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <button @click="showProfileInfo = false" class="mt-6 w-full py-2.5 rounded-lg border border-white/10 hover:bg-white/5 transition-colors text-sm font-medium text-secondary hover:text-white">
                    Close Details
                </button>
            @endif
        </div>
    </div>

    <style>
        /* Custom Scrollbar for Chat */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px; /* For horizontal scroll */
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
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
            const chatContainer = document.querySelectorAll('.custom-scrollbar')[1]; // Adjust index if needed or use ID
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
            
            Livewire.on('messageSent', () => {
                 const chatContainer = document.querySelectorAll('.custom-scrollbar')[1];
                 if (chatContainer) {
                    setTimeout(() => {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }, 100);
                 }
            });
        });
    </script>
</div>