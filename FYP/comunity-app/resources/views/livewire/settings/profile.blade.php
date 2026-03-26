<div style="background:#0f172a; min-height:100vh;">
    @push('styles')
    <style>
        /* ── Layout ── */
        .prof-page { max-width: 1100px; margin: 0 auto; padding: 2rem; }

        /* ── Shared card base ── */
        .prof-card {
            background: rgba(30,41,59,.65);
            border: 1px solid rgba(71,85,105,.3);
            border-radius: 18px;
            overflow: hidden;
        }
        .prof-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(71,85,105,.2);
            display: flex; align-items: center; gap: 10px;
            background: rgba(15,23,42,.4);
        }
        .prof-card-header-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .prof-card-header h3 { font-size: 15px; font-weight: 800; color: #e2e8f0; letter-spacing:-.01em; }
        .prof-card-body { padding: 1.5rem; }

        /* ── Input ── */
        .prof-label {
            display: block; font-size: 10px; font-weight: 800;
            letter-spacing: .08em; text-transform: uppercase; color: #475569;
            margin-bottom: 6px;
        }
        .prof-input {
            width: 100%; padding: 10px 14px;
            background: rgba(15,23,42,.7); border: 1px solid rgba(71,85,105,.4);
            border-radius: 10px; color: #e2e8f0; font-size: 14px; outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .prof-input:focus {
            border-color: rgba(99,102,241,.55);
            box-shadow: 0 0 0 3px rgba(99,102,241,.1);
        }
        .prof-input::placeholder { color: #334155; }

        /* ── Save button ── */
        .prof-save-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 700;
            color: #fff; cursor: pointer; border: none;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            box-shadow: 0 4px 14px rgba(99,102,241,.32);
            transition: transform .18s, box-shadow .18s;
        }
        .prof-save-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.42); }

        /* ── Avatar ── */
        .prof-avatar-wrap { position: relative; display: inline-block; }
        .prof-avatar {
            width: 96px; height: 96px; border-radius: 50%;
            overflow: hidden; border: 3px solid rgba(99,102,241,.4);
            background: rgba(30,41,59,.8);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800; color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .prof-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .prof-avatar-overlay {
            position: absolute; inset: 0; border-radius: 50%;
            background: rgba(0,0,0,.55); backdrop-filter: blur(3px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; cursor: pointer;
            transition: opacity .2s;
        }
        .prof-avatar-wrap:hover .prof-avatar-overlay { opacity: 1; }

        /* ── Role badge ── */
        .prof-role-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 8px; font-size: 10px;
            font-weight: 800; letter-spacing: .07em; text-transform: uppercase;
        }

        /* ── Status badges ── */
        .badge-approved { background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.25); }
        .badge-pending  { background:rgba(245,158,11,.12);  color:#fbbf24; border:1px solid rgba(245,158,11,.25); }
        .badge-rejected { background:rgba(239,68,68,.12);   color:#f87171; border:1px solid rgba(239,68,68,.25); }

        /* ── Danger zone ── */
        .prof-danger {
            background: rgba(30,18,22,.7); border: 1px solid rgba(239,68,68,.18); border-radius: 18px;
        }
        .prof-danger-header {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(239,68,68,.15);
            display: flex; align-items: center; gap: 10px;
        }
        .prof-danger-header h3 { font-size: 15px; font-weight: 800; color: #f87171; }
        .prof-danger-body { padding: 1.5rem; }
    </style>
    @endpush

    <div class="prof-page">

        {{-- ── Page heading ── --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.75rem;">
            <div style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(99,102,241,.35);">
                <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:20px;font-weight:800;color:#f1f5f9;letter-spacing:-.02em;line-height:1;">My Profile</h1>
                <p style="font-size:12px;color:#475569;margin-top:2px;">Manage your account settings and family members.</p>
            </div>
        </div>

        {{-- ── Profile form ── --}}
        <form wire:submit="updateProfileInformation">
            <div style="display:grid;grid-template-columns:280px 1fr;gap:1.25rem;align-items:start;margin-bottom:1.25rem;">

                {{-- Left: Avatar card --}}
                <div class="prof-card">
                    <div class="prof-card-header">
                        <div class="prof-card-header-icon" style="background:rgba(99,102,241,.15);">
                            <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3>Account</h3>
                    </div>
                    <div class="prof-card-body" style="display:flex;flex-direction:column;align-items:center;text-align:center;gap:14px;">
                        {{-- Avatar --}}
                        <div class="prof-avatar-wrap">
                            <div class="prof-avatar">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}">
                                @elseif (auth()->user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                            <label for="photo" class="prof-avatar-overlay">
                                <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                            </label>
                            <input wire:model="photo" id="photo" type="file" class="hidden" accept="image/*">
                        </div>

                        {{-- Name & role --}}
                        <div>
                            <div style="font-size:16px;font-weight:800;color:#f1f5f9;margin-bottom:6px;">{{ auth()->user()->name }}</div>
                            <span class="prof-role-badge" style="background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.25);">
                                <span style="width:5px;height:5px;border-radius:50%;background:#818cf8;display:inline-block;"></span>
                                {{ ucfirst(auth()->user()->user_type) }}
                            </span>
                        </div>

                        {{-- Address info (read-only display) --}}
                        @if(auth()->user()->block || auth()->user()->unit_number)
                        <div style="width:100%;background:rgba(15,23,42,.5);border:1px solid rgba(71,85,105,.25);border-radius:10px;padding:10px 14px;text-align:left;">
                            <div style="font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Residence</div>
                            <div style="font-size:13px;color:#94a3b8;">
                                @if(auth()->user()->block)Block {{ auth()->user()->block }}@endif
                                @if(auth()->user()->unit_number), Unit {{ auth()->user()->unit_number }}@endif
                            </div>
                            @if(auth()->user()->street)
                            <div style="font-size:12px;color:#475569;margin-top:2px;">{{ auth()->user()->street }}</div>
                            @endif
                        </div>
                        @endif

                        {{-- Email --}}
                        <div style="width:100%;background:rgba(15,23,42,.5);border:1px solid rgba(71,85,105,.25);border-radius:10px;padding:10px 14px;text-align:left;">
                            <div style="font-size:10px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Email</div>
                            <div style="font-size:13px;color:#94a3b8;word-break:break-all;">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                {{-- Right: Personal info fields --}}
                <div class="prof-card">
                    <div class="prof-card-header">
                        <div class="prof-card-header-icon" style="background:rgba(99,102,241,.15);">
                            <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h3>Edit Personal Details</h3>
                    </div>
                    <div class="prof-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                            <div style="grid-column:span 2;">
                                <label class="prof-label">Full Name</label>
                                <input wire:model="name" type="text" class="prof-input" placeholder="Your full name">
                                @error('name') <span style="color:#f87171;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                            </div>
                            <div style="grid-column:span 2;">
                                <label class="prof-label">Email Address</label>
                                <input wire:model="email" type="email" class="prof-input" placeholder="your@email.com">
                                @error('email') <span style="color:#f87171;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="prof-label">Block</label>
                                <input wire:model="block" type="text" class="prof-input" placeholder="e.g. A">
                            </div>
                            <div>
                                <label class="prof-label">Unit Number</label>
                                <input wire:model="unit_number" type="text" class="prof-input" placeholder="e.g. 12-3">
                            </div>
                            <div style="grid-column:span 2;">
                                <label class="prof-label">Street</label>
                                <input wire:model="street" type="text" class="prof-input" placeholder="Street name">
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:12px;padding-top:.25rem;border-top:1px solid rgba(71,85,105,.2);margin-top:.25rem;">
                            <button type="submit" class="prof-save-btn">
                                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                            <x-action-message on="profile-updated"
                                style="font-size:12px;font-weight:700;color:#34d399;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);padding:5px 14px;border-radius:8px;display:flex;align-items:center;gap:6px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Saved!
                            </x-action-message>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ── Family Hub ── --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.25rem;margin-top:.5rem;">
            <div style="width:38px;height:38px;border-radius:11px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.25);display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:16px;font-weight:800;color:#f1f5f9;letter-spacing:-.01em;line-height:1;">Family Hub</h2>
                <p style="font-size:12px;color:#475569;margin-top:2px;">Manage registered family members under your household.</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:280px 1fr;gap:1.25rem;align-items:start;margin-bottom:1.25rem;">

            {{-- Add Member form --}}
            <div class="prof-card">
                <div class="prof-card-header">
                    <div class="prof-card-header-icon" style="background:rgba(16,185,129,.12);">
                        <svg width="16" height="16" fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3>Add Member</h3>
                </div>
                <div class="prof-card-body">
                    {{-- Info banner --}}
                    <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:10px;margin-bottom:1.1rem;">
                        <svg width="14" height="14" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24" style="shrink:0;margin-top:1px;">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p style="font-size:12px;color:#d97706;">New accounts require admin approval before activation.</p>
                    </div>

                    <form wire:submit="registerFamilyMember" style="display:flex;flex-direction:column;gap:.85rem;">
                        <div>
                            <label class="prof-label">Full Name</label>
                            <input wire:model="newFamilyName" type="text" class="prof-input" placeholder="Member's name">
                            @error('newFamilyName') <span style="color:#f87171;font-size:11px;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="prof-label">Email</label>
                            <input wire:model="newFamilyEmail" type="email" class="prof-input" placeholder="member@email.com">
                            @error('newFamilyEmail') <span style="color:#f87171;font-size:11px;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="prof-label">Password</label>
                            <input wire:model="newFamilyPassword" type="password" class="prof-input" placeholder="Min. 8 characters">
                            @error('newFamilyPassword') <span style="color:#f87171;font-size:11px;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit"
                            style="width:100%;padding:10px;border-radius:10px;font-size:13px;font-weight:700;color:#fff;border:none;cursor:pointer;background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 14px rgba(16,185,129,.25);transition:transform .18s,box-shadow .18s;margin-top:.2rem;"
                            onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                            Register Account
                        </button>
                        <x-action-message on="family-member-added"
                            style="font-size:12px;font-weight:700;color:#34d399;text-align:center;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);padding:6px;border-radius:8px;">
                            Member registered successfully!
                        </x-action-message>
                    </form>
                </div>
            </div>

            {{-- Member list --}}
            <div class="prof-card">
                <div class="prof-card-header">
                    <div class="prof-card-header-icon" style="background:rgba(99,102,241,.12);">
                        <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3>Registered Members</h3>
                    @if($familyMembers->count() > 0)
                        <span style="margin-left:auto;font-size:10px;font-weight:800;color:#818cf8;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.25);padding:3px 10px;border-radius:8px;">
                            {{ $familyMembers->count() }} {{ Str::plural('member', $familyMembers->count()) }}
                        </span>
                    @endif
                </div>
                <div class="prof-card-body">
                    @if($familyMembers->isEmpty())
                        <div style="text-align:center;padding:2.5rem 1rem;">
                            <div style="width:48px;height:48px;border-radius:14px;background:rgba(71,85,105,.12);border:1px dashed rgba(71,85,105,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="22" height="22" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p style="font-size:14px;font-weight:700;color:#475569;">No family members yet</p>
                            <p style="font-size:12px;color:#334155;margin-top:4px;">Register a member using the form on the left.</p>
                        </div>
                    @else
                        <div style="display:flex;flex-direction:column;gap:.65rem;">
                            @foreach($familyMembers as $member)
                                @php
                                    $init = strtoupper(substr($member->name, 0, 1));
                                    $colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ec4899'];
                                    $color  = $colors[$loop->index % count($colors)];
                                @endphp
                                <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:rgba(15,23,42,.5);border:1px solid rgba(71,85,105,.2);border-radius:12px;transition:border-color .18s;"
                                    onmouseover="this.style.borderColor='rgba(99,102,241,.35)'"
                                    onmouseout="this.style.borderColor='rgba(71,85,105,.2)'">
                                    {{-- Avatar --}}
                                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;background:{{ $color }}33;border:2px solid {{ $color }}55;">
                                        {{ $init }}
                                    </div>
                                    {{-- Info --}}
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:14px;font-weight:700;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->name }}</div>
                                        <div style="font-size:12px;color:#475569;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $member->email }}</div>
                                    </div>
                                    {{-- Status --}}
                                    @if($member->status === 'approved')
                                        <span class="prof-role-badge badge-approved">
                                            <span style="width:5px;height:5px;border-radius:50%;background:#34d399;animation:pulse 2s infinite;display:inline-block;"></span>
                                            Active
                                        </span>
                                    @elseif($member->status === 'rejected')
                                        <span class="prof-role-badge badge-rejected">Rejected</span>
                                    @else
                                        <span class="prof-role-badge badge-pending">
                                            <span style="width:5px;height:5px;border-radius:50%;background:#fbbf24;animation:pulse 2s infinite;display:inline-block;"></span>
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Danger Zone ── --}}
        <div class="prof-danger">
            <div class="prof-danger-header">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <h3>Danger Zone</h3>
            </div>
            <div class="prof-danger-body">
                <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.15);border-radius:10px;margin-bottom:1.1rem;max-width:520px;">
                    <svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <p style="font-size:12px;color:#fca5a5;">Permanently deletes your account and all associated data. <strong>This cannot be undone.</strong></p>
                </div>
                <livewire:settings.delete-user-form />
            </div>
        </div>

    </div>{{-- /prof-page --}}
</div>