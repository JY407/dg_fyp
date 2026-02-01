<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $unit_number = '';
    public string $block = '';
    public string $street = '';
    public string $user_type = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        \Log::info('Registration attempt', ['email' => $this->email]);

        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'unit_number' => ['required', 'string', 'max:50'],
                'block' => ['nullable', 'string', 'max:50'],
                'street' => ['nullable', 'string', 'max:100'],
                'user_type' => ['required', 'in:tenant,owner'],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ]);

            \Log::info('Validation passed', ['validated_keys' => array_keys($validated)]);

            // Password will be automatically hashed by the User model's 'hashed' cast
            // No need to manually hash it here
            $user = User::create($validated);

            \Log::info('User created', ['user_id' => $user->id]);

            event(new Registered($user));

            Session::flash('status', 'Registration successful! Please log in.');

            $this->redirect(route('login'), navigate: true);
        } catch (\Exception $e) {
            \Log::error('Registration failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.auth.register');
    }
}
