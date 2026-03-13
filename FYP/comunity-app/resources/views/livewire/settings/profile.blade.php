<div class="min-h-screen bg-[#131520] font-sans relative overflow-hidden" style="padding: 120px 0 80px;">

    <div class="container mx-auto px-6 relative z-10 max-w-6xl">
        <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400 mb-2">My Profile</h1>
        <p class="text-gray-400 text-lg mb-10">Manage your account settings and family members.</p>

        <form wire:submit="updateProfileInformation">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Photo & Role -->
                <!-- Photo & Role -->
                <div class="md:col-span-1">
                    <div class="bg-[#1e2133] rounded-[24px] p-8 text-center pb-10">
                        <div class="relative inline-block mb-6">
                            <!-- Avatar Ring -->
                            <div class="absolute inset-[-4px] rounded-full bg-gradient-to-b from-[#4bc0c8] to-[#2c3e50] opacity-80"></div>
                            
                            <div class="relative w-32 h-32 rounded-full overflow-hidden mx-auto bg-[#131520] flex items-center justify-center text-4xl font-bold text-white border-4 border-[#1e2133]">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif (auth()->user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                            <!-- Image Overlay Upload Button -->
                            <label for="photo" class="absolute inset-[4px] flex items-center justify-center bg-black/60 rounded-full opacity-0 hover:opacity-100 cursor-pointer transition-all duration-300 backdrop-blur-sm z-20">
                                <span class="text-sm font-bold text-white flex flex-col items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    Update
                                </span>
                            </label>
                            <input wire:model="photo" id="photo" type="file" class="hidden" accept="image/*">
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white mb-3">{{ auth()->user()->name }}</h3>
                        <div class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wider bg-[#32345e] text-[#8e95e8] uppercase">
                            {{ ucfirst(auth()->user()->user_type) }}
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="md:col-span-2">
                    <div class="bg-[#1e2133] rounded-[24px] p-8 pb-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-full bg-[#2f3252] flex items-center justify-center text-[#878edf]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <h3 class="text-[28px] font-bold text-white">Personal Details</h3>
                        </div>
                        
                        <div class="space-y-5">
                            <div class="max-w-[800px]">
                                <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Full Name</label>
                                <input wire:model="name" type="text" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                                @error('name') <span class="text-red-400 text-xs mt-1 block font-medium ml-5">{{ $message }}</span> @enderror
                            </div>
                            <div class="max-w-[800px]">
                                <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Email Address</label>
                                <input wire:model="email" type="email" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                                @error('email') <span class="text-red-400 text-xs mt-1 block font-medium ml-5">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-10 gap-x-6 gap-y-5 max-w-[800px]">
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Block</label>
                                    <input wire:model="block" type="text" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Unit Number</label>
                                    <input wire:model="unit_number" type="text" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Street</label>
                                    <input wire:model="street" type="text" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                                </div>
                            </div>

                            <div class="pt-4 flex items-center gap-4">
                                <button type="submit" class="bg-[#4d4872] hover:bg-[#3d385a] text-white font-bold py-2.5 px-6 rounded-full transition-colors flex items-center gap-2 text-sm shadow-md min-w-[160px] justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Save Changes
                                </button>
                                <x-action-message on="profile-updated" class="text-green-400 font-medium bg-[#14261f] px-4 py-2 rounded-full border border-green-500/20 flex items-center gap-2 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </x-action-message>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Family Members -->
        <h2 class="text-[32px] font-extrabold mt-16 mb-8 text-white flex items-center gap-6">
            Family Hub
            <div class="h-[1px] flex-1 bg-[#26293d]"></div>
        </h2>
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-12">
            <!-- Register Form -->
            <div class="xl:col-span-1">
                <div class="bg-[#1e2133] rounded-[24px] p-8 pb-10 h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="text-[#878edf]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        <h3 class="text-[28px] font-bold text-white">Add Member</h3>
                    </div>

                    <div class="bg-[#181a28] rounded-xl p-4 mb-8 flex items-start gap-3 border border-[#26293d]">
                       <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="text-yellow-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                       <p class="text-sm text-[#c8cbd9]">Accounts need admin approval.</p>
                    </div>
                    
                    <form wire:submit="registerFamilyMember" class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Full Name</label>
                            <input wire:model="newFamilyName" type="text" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Email Address</label>
                            <input wire:model="newFamilyEmail" type="email" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-widest text-[#737a9c] mb-1.5 ml-5">Password</label>
                            <input wire:model="newFamilyPassword" type="password" class="w-full bg-[#0f111a] border-none rounded-full px-5 py-3.5 text-white text-[15px] focus:ring-2 focus:ring-[#4d4cea] focus:outline-none transition-colors">
                        </div>
                        
                        <button type="submit" class="w-full bg-[#5d5cfc] hover:bg-[#4d4cea] text-white font-bold py-3.5 px-6 rounded-full transition-colors mt-8 text-sm shadow-md shadow-[#5d5cfc]/20 flex items-center justify-center min-w-[160px]">
                            Register Account
                        </button>
                        <x-action-message on="family-member-added" class="text-green-400 text-sm font-medium text-center block bg-[#14261f] py-2 rounded-full border border-green-500/20 mt-4" />
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="xl:col-span-2">
                <div class="bg-[#1e2133] rounded-[24px] p-8 pb-10 min-h-full">
                    <h3 class="text-[28px] font-bold text-white mb-8 flex items-center gap-3 border-b border-[#26293d] pb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="text-[#878edf]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Registered Accounts
                    </h3>
                    
                    @if($familyMembers->isEmpty())
                        <div class="text-[#737a9c] text-sm text-center py-12 bg-[#181a28] rounded-2xl border border-[#26293d] border-dashed border-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" class="mx-auto mb-3 text-[#393e5e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                            No family members found.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($familyMembers as $member)
                                <div class="flex items-center justify-between p-5 bg-[#181a28] hover:bg-[#1a1c2b] rounded-2xl border border-[#26293d] transition-colors">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 rounded-full bg-[#2f3252] text-[#878edf] flex items-center justify-center font-bold text-lg">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-white text-base font-bold mb-0.5">{{ $member->name }}</div>
                                            <div class="text-[#737a9c] text-sm flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                {{ $member->email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($member->status === 'approved')
                                            <span class="text-[11px] font-black tracking-wider text-[#4ade80] bg-[#1a2f24] border border-[#22c55e]/30 px-3 py-1.5 rounded-full flex items-center gap-1.5 uppercase">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#4ade80] animate-pulse"></div> Active
                                            </span>
                                        @elseif($member->status === 'rejected')
                                            <span class="text-[11px] font-black tracking-wider text-[#f87171] bg-[#361f22] border border-[#ef4444]/30 px-3 py-1.5 rounded-full flex items-center gap-1.5 uppercase">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#f87171]"></div> Rejected
                                            </span>
                                        @else
                                            <span class="text-[11px] font-black tracking-wider text-[#facc15] bg-[#332e18] border border-[#eab308]/30 px-3 py-1.5 rounded-full flex items-center gap-1.5 uppercase">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#facc15] animate-pulse"></div> Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-[#231b25] rounded-[24px] border border-[#3d232c] p-8 pb-10 mt-8 group transition-colors hover:border-[#522b37]">
            <div class="relative z-10">
                <h3 class="text-[28px] font-bold text-[#f87171] mb-3 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18"/><path d="M4 4v16c0 1.1.9 2 2 2h12c.5 0 1-.2 1.4-.5"/><path d="M20 16V4c0-1.1-.9-2-2-2H8c-.5 0-1 .2-1.4.5"/></svg>
                    Danger Zone
                </h3>
                <div class="text-[#c8cbd9] text-sm mb-8 flex items-start gap-3 max-w-2xl bg-[#1e171f] p-4 rounded-xl border border-[#3d232c]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="text-red-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Permanently delete your account and all associated data. This action is not reversible.
                </div>
                <livewire:settings.delete-user-form />
            </div>
        </div>
    </div>
</div>