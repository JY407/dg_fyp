<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    public $photo;
    public string $name = '';
    public string $email = '';
    public string $unit_number = '';
    public string $block = '';
    public string $street = '';

    // Family Member Registration
    public string $newFamilyName = '';
    public string $newFamilyEmail = '';
    public string $newFamilyPassword = '';

    // Family Members List
    public $familyMembers;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->unit_number = $user->unit_number ?? '';
        $this->block = $user->block ?? '';
        $this->street = $user->street ?? '';

        $this->refreshFamilyMembers();
    }

    public function refreshFamilyMembers()
    {
        $this->familyMembers = Auth::user()->familyMembers()->get();
    }

    public function registerFamilyMember()
    {
        $this->validate([
            'newFamilyName' => ['required', 'string', 'max:255'],
            'newFamilyEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'newFamilyPassword' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $this->newFamilyName,
            'email' => $this->newFamilyEmail,
            'password' => bcrypt($this->newFamilyPassword),
            'user_type' => 'tenant', // Default family members to tenants
            'status' => 'pending',   // Pending verification
            'created_by' => Auth::id(),
            'unit_number' => $this->unit_number,
            'block' => $this->block,
            'street' => $this->street,
        ]);

        $this->reset(['newFamilyName', 'newFamilyEmail', 'newFamilyPassword']);
        $this->refreshFamilyMembers();
        $this->dispatch('family-member-added');
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'photo' => ['nullable', 'image', 'max:1024'], // 1MB Max
            'unit_number' => ['nullable', 'string', 'max:255'],
            'block' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
        ]);

        // Handle Photo Upload
        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'unit_number' => $validated['unit_number'],
            'block' => $validated['block'],
            'street' => $validated['street'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.settings.profile');
    }
}
