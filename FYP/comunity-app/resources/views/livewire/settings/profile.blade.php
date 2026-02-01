<div style="padding: 120px 0 80px; margin-top: 70px;">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold mb-2">My Profile</h1>
        <p class="text-gray-400 mb-8">Manage your account settings and family members.</p>

        <form wire:submit="updateProfileInformation">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Photo & Role -->
                <div class="md:col-span-1">
                    <div class="glass-card text-center p-6">
                        <div class="relative inline-block mb-4 group">
                            <div class="w-24 h-24 rounded-full overflow-hidden mx-auto bg-gray-800 flex items-center justify-center text-3xl font-bold text-white border-2 border-white/10">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif (auth()->user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                            <label for="photo" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                <span class="text-xs text-white">Change</span>
                            </label>
                            <input wire:model="photo" id="photo" type="file" class="hidden" accept="image/*">
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ auth()->user()->name }}</h3>
                        <div class="inline-block px-3 py-1 mt-2 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 uppercase">
                            {{ ucfirst(auth()->user()->user_type) }}
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="md:col-span-2">
                    <div class="glass-card p-6">
                        <h3 class="text-xl font-bold text-white mb-6 border-b border-white/10 pb-4">Personal Details</h3>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Name</label>
                                <input wire:model="name" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-indigo-500 focus:outline-none">
                                @error('name') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                                <input wire:model="email" type="email" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-indigo-500 focus:outline-none">
                                @error('email') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Block</label>
                                    <input wire:model="block" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Unit</label>
                                    <input wire:model="unit_number" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Street</label>
                                    <input wire:model="street" type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <x-action-message on="profile-updated" class="text-green-400 text-sm" />
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Family Members -->
        <h2 class="text-2xl font-bold mt-12 mb-6">Family Members</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Register Form -->
            <div class="md:col-span-1">
                <div class="glass-card p-6">
                    <h3 class="text-lg font-bold text-white mb-2">Register Family</h3>
                    <p class="text-xs text-gray-400 mb-4">Accounts need admin approval.</p>
                    
                    <form wire:submit="registerFamilyMember" class="space-y-3">
                        <input wire:model="newFamilyName" type="text" placeholder="Name" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                        <input wire:model="newFamilyEmail" type="email" placeholder="Email" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                        <input wire:model="newFamilyPassword" type="password" placeholder="Password" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg text-sm transition-colors">
                            Register
                        </button>
                        <x-action-message on="family-member-added" class="text-green-400 text-xs text-center block" />
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="md:col-span-2">
                <div class="glass-card p-6 min-h-full">
                    <h3 class="text-lg font-bold text-white mb-4">Registered Accounts</h3>
                    @if($familyMembers->isEmpty())
                        <div class="text-gray-500 text-sm text-center py-4">No family members found.</div>
                    @else
                        <div class="space-y-3">
                            @foreach($familyMembers as $member)
                                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-900 text-indigo-300 flex items-center justify-center font-bold text-sm">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium">{{ $member->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($member->status === 'approved')
                                            <span class="text-xs font-bold text-green-400 bg-green-500/10 px-2 py-1 rounded">Active</span>
                                        @elseif($member->status === 'rejected')
                                            <span class="text-xs font-bold text-red-400 bg-red-500/10 px-2 py-1 rounded">Rejected</span>
                                        @else
                                            <span class="text-xs font-bold text-yellow-400 bg-yellow-500/10 px-2 py-1 rounded">Pending</span>
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
        <div class="glass-card p-6 border-red-500/20">
            <h3 class="text-lg font-bold text-red-400 mb-2">Delete Account</h3>
            <div class="text-gray-400 text-sm mb-4">
                Permanently delete your account and all data.
            </div>
            <livewire:settings.delete-user-form />
        </div>
    </div>
</div>