<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

new #[Layout('layouts.admin')] class extends Component {
    public $permissions = [];
    public $activeTab = 'permissions'; // permissions, guards
    
    // User role update state
    public $searchEmail = '';
    public $newGuardName = '';
    public $newGuardEmail = '';
    public $newGuardPassword = '';

    public function mount()
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access. Only community administrators can access this module.');
        }
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $filePath = 'security_permissions.json';
        if (Storage::exists($filePath)) {
            $this->permissions = json_decode(Storage::get($filePath), true);
        } else {
            // Default admin module permissions for security guards
            $this->permissions = [
                'dashboard'       => true,
                'visitors'        => true,
                'verifications'   => false,
                'duty_roster'     => true,
                'services'        => false,
                'culture'         => false,
                'events'          => false,
                'messages'        => false,
                'facilities'      => false,
                'road_notices'    => true,
                'announcements'   => true,
                'forum'           => false,
                'emergencies'     => true,
            ];
            Storage::put($filePath, json_encode($this->permissions, JSON_PRETTY_PRINT));
        }
    }

    public function savePermissions()
    {
        Storage::put('security_permissions.json', json_encode($this->permissions, JSON_PRETTY_PRINT));
        session()->flash('success', 'Security Guard admin panel module access permissions updated.');
    }

    public function togglePermission($key)
    {
        if (isset($this->permissions[$key])) {
            $this->permissions[$key] = !$this->permissions[$key];
        } else {
            $this->permissions[$key] = true;
        }
    }

    public function promoteToSecurity($userId)
    {
        $user = User::findOrFail($userId);
        $user->user_type = 'security';
        $user->save();
        
        session()->flash('user_success', "{$user->name} has been promoted to Security Guard role.");
        $this->reset('searchEmail');
    }

    public function demoteFromSecurity($userId)
    {
        $user = User::findOrFail($userId);
        $user->user_type = 'tenant';
        $user->save();
        
        session()->flash('user_success', "{$user->name} has been demoted back to Tenant role.");
    }

    public function registerGuard()
    {
        $this->validate([
            'newGuardName'  => 'required|string|max:255',
            'newGuardEmail' => 'required|email|unique:users,email',
            'newGuardPassword' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $this->newGuardName,
            'email' => $this->newGuardEmail,
            'password' => Hash::make($this->newGuardPassword),
            'user_type' => 'security',
            'status' => 'approved',
        ]);

        session()->flash('user_success', "New guard account created successfully for {$user->name}.");
        $this->reset(['newGuardName', 'newGuardEmail', 'newGuardPassword']);
    }

    public function with()
    {
        // Search user by email for role assignment
        $searchedUser = null;
        if (!empty($this->searchEmail)) {
            $searchedUser = User::where('email', 'like', "%{$this->searchEmail}%")->where('user_type', '!=', 'admin')->first();
        }

        return [
            'securityUsers' => User::where('user_type', 'security')->latest()->get(),
            'searchedUser' => $searchedUser
        ];
    }
}; ?>

<div class="p-6 min-h-screen" style="background: #0f172a;">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Security Access Control</h1>
        <p class="text-gray-400 text-sm mt-1">Configure admin dashboard module access permissions and manage security personnel accounts.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-gray-700/60 mb-6 gap-6 text-sm font-medium">
        <button wire:click="$set('activeTab', 'permissions')" 
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $activeTab === 'permissions' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            🛡️ Admin Dashboard Permissions
        </button>
        <button wire:click="$set('activeTab', 'guards')" 
            class="pb-3 border-b-2 transition-colors cursor-pointer {{ $activeTab === 'guards' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            👮 Active Guards Users ({{ \App\Models\User::where('user_type', 'security')->count() }})
        </button>
    </div>

    @if($activeTab === 'permissions')
        {{-- Permission Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-slate-800 border border-gray-700/50 rounded-2xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-white mb-1">Admin Module Controls</h2>
                <p class="text-xs text-slate-400 mb-6">Select which administrative screens and dashboards are accessible to logged-in security guards.</p>
                
                <div class="space-y-4">
                    @php
                        $features = [
                            'dashboard'     => ['title' => 'Admin Dashboard', 'desc' => 'Main landing page displaying analytics widgets and quick summaries.', 'icon' => '📊'],
                            'visitors'      => ['title' => 'Record Visitor', 'desc' => 'Creating guest check-in passes and scanning active visitor entries.', 'icon' => '🎫'],
                            'verifications' => ['title' => 'Pending Verifications', 'desc' => 'Approving or rejecting new resident account signups.', 'icon' => '📝'],
                            'duty_roster'   => ['title' => 'Security Duty Roster', 'desc' => 'Scheduling security guards shifts, assignments, and posts.', 'icon' => '🛡️'],
                            'services'      => ['title' => 'Services Management', 'desc' => 'Administering community services, bookings, and repairs.', 'icon' => '🛠️'],
                            'culture'       => ['title' => 'Culture Management', 'desc' => 'Managing Malaysian cultural guides, histories, and event calendars.', 'icon' => '📅'],
                            'events'        => ['title' => 'Events Management', 'desc' => 'Scheduling and creating community holiday gatherings.', 'icon' => '🎉'],
                            'messages'      => ['title' => 'Contact Messages', 'desc' => 'Reading and addressing general public queries and logs.', 'icon' => '✉️'],
                            'facilities'    => ['title' => 'Facilities Management', 'desc' => 'Overseeing facility booking schedules and statuses.', 'icon' => '🏛️'],
                            'road_notices'  => ['title' => 'Road Notices', 'desc' => 'Creating, editing, and broadcasting road blockades or closures.', 'icon' => '🚧'],
                            'announcements' => ['title' => 'Announcements Management', 'desc' => 'Managing and posting general community announcement boards.', 'icon' => '📢'],
                            'forum'         => ['title' => 'Forum Management', 'desc' => 'Moderating neighborhood threads, posts, and replies.', 'icon' => '💬'],
                            'emergencies'   => ['title' => 'Emergencies Management', 'desc' => 'Accessing real-time panic alarm lists and status controls.', 'icon' => '🚨'],
                        ];
                    @endphp

                    @foreach($features as $key => $f)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900/50 border border-slate-700/30 hover:border-slate-700/60 transition-colors">
                            <div class="flex items-start gap-3.5 pr-4">
                                <span class="text-2xl mt-0.5 shrink-0">{{ $f['icon'] }}</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-200">{{ $f['title'] }}</h3>
                                    <p class="text-xs text-slate-400 leading-relaxed mt-0.5">{{ $f['desc'] }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:click="togglePermission('{{ $key }}')" {{ $permissions[$key] ?? false ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <button wire:click="savePermissions" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Save Permissions
                    </button>
                </div>
            </div>

            {{-- Explanatory Panel --}}
            <div class="bg-slate-800 border border-gray-700/50 rounded-2xl p-6 shadow-xl h-fit flex flex-col gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <span class="text-xl">👮</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">How Guard Permissions Work</h2>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Security guards log in directly and access the **Admin Dashboard** panel (not the user dashboard). By configuring these toggles:
                    </p>
                    <ul class="text-xs text-slate-400 space-y-2 mt-3 list-disc pl-4 leading-relaxed">
                        <li>**Sidebar Isolation**: Only the checked admin menu items will be rendered inside the `admin-sidebar`.</li>
                        <li>**Hardened Router Lock**: If a guard directly enters a URL route for a checked-off module, they are immediately stopped with a `403 Forbidden` screen.</li>
                        <li>Ensures guards can only perform approved administrative actions (e.g. record visitors, broadcast road notices, view shifts).</li>
                    </ul>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'guards')
        {{-- Guards Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- List --}}
            <div class="lg:col-span-2 bg-slate-800 border border-gray-700/50 rounded-2xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-white mb-4">Active Security Guards</h2>
                
                @if (session()->has('user_success'))
                    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-2.5 rounded-xl flex items-center gap-2">
                        <span class="text-xs font-semibold">{{ session('user_success') }}</span>
                    </div>
                @endif

                @if($securityUsers->isEmpty())
                    <div class="text-center py-10 bg-slate-900/40 rounded-xl border border-slate-700/20 text-gray-500 text-sm">
                        No security guards registered yet. Use the right panel to assign guards!
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach($securityUsers as $guard)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900/50 border border-slate-700/30">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600/80 text-white font-bold flex items-center justify-center text-sm">
                                        {{ substr($guard->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-200">{{ $guard->name }}</h3>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $guard->email }}</p>
                                    </div>
                                </div>
                                <button wire:click="demoteFromSecurity({{ $guard->id }})" wire:confirm="Revoke security guard privileges for {{ $guard->name }}?" 
                                    class="text-xs text-red-400 hover:text-red-300 font-semibold px-3 py-1.5 rounded-lg border border-red-500/20 hover:bg-red-500/10 transition-colors cursor-pointer">
                                    Revoke Guard Role
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Assign Role / Register Panel --}}
            <div class="flex flex-col gap-6">
                {{-- Assign existing --}}
                <div class="bg-slate-800 border border-gray-700/50 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-base font-bold text-white mb-1">Assign Guard Privileges</h2>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Search an existing user by email to assign security guard privileges.</p>
                    
                    <input type="text" wire:model.live="searchEmail" class="w-full text-xs rounded-xl border-gray-700 bg-slate-900 text-white p-3 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Search email...">
                    
                    @if($searchedUser)
                        <div class="mt-4 p-3 rounded-xl bg-indigo-950/20 border border-indigo-500/20 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">{{ $searchedUser->name }}</h4>
                                <p class="text-[10px] text-slate-400">{{ $searchedUser->email }}</p>
                            </div>
                            <button wire:click="promoteToSecurity({{ $searchedUser->id }})" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                Promote
                            </button>
                        </div>
                    @elseif(!empty($searchEmail))
                        <div class="text-[11px] text-red-400 mt-2 text-center">No matching user found.</div>
                    @endif
                </div>

                {{-- Create new --}}
                <div class="bg-slate-800 border border-gray-700/50 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-base font-bold text-white mb-1">Register New Guard</h2>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Create a brand new guard account directly.</p>
                    
                    <form wire:submit.prevent="registerGuard" class="space-y-3.5">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Guard Name</label>
                            <input type="text" wire:model="newGuardName" class="w-full text-xs rounded-xl border-gray-700 bg-slate-900 text-white p-3 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Officer Name">
                            @error('newGuardName') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Email Address</label>
                            <input type="email" wire:model="newGuardEmail" class="w-full text-xs rounded-xl border-gray-700 bg-slate-900 text-white p-3 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="guard@lcare.com">
                            @error('newGuardEmail') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Temporary Password</label>
                            <input type="password" wire:model="newGuardPassword" class="w-full text-xs rounded-xl border-gray-700 bg-slate-900 text-white p-3 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Minimum 6 characters">
                            @error('newGuardPassword') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition-all hover:-translate-y-0.5 shadow-md shadow-indigo-600/25 cursor-pointer">
                            Register Guard
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @endif
</div>
