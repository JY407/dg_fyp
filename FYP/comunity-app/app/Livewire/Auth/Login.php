<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        \Log::info('Login attempt', ['email' => $this->email]);

        try {
            $this->validate();
            \Log::info('Login validation passed');

            $this->ensureIsNotRateLimited();

            $user = $this->validateCredentials();
            \Log::info('Credentials validated', ['user_id' => $user->id]);

            if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
                \Log::info('Redirecting to 2FA');
                Session::put([
                    'login.id' => $user->getKey(),
                    'login.remember' => $this->remember,
                ]);

                $this->redirect(route('two-factor.login'), navigate: true);

                return;
            }

            Auth::login($user, $this->remember);
            \Log::info('User logged in');

            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            \Log::info('Session regenerated, redirecting to dashboard');

            if ($user->user_type === 'admin') {
                $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
                return;
            }

            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        } catch (\Exception $e) {
            \Log::error('Login failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        /** @var User|null $user */
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.auth.login');
    }
}
