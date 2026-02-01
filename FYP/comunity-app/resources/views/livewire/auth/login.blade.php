<div class="glass-card">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Sign in to access your community dashboard</p>
    </div>

    @if (session('status'))
        <div
            style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.75rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center; font-size: 0.9rem; backdrop-filter: blur(5px);">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input wire:model="email" id="email" type="email" class="form-input" required autofocus
                autocomplete="username" placeholder="john@example.com">
            @error('email')
                <span class="error-message">⚠️ {{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input wire:model="password" id="password" type="password" class="form-input" required
                autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <span class="error-message">⚠️ {{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex-between">
            <label for="remember_me" class="checkbox-wrapper">
                <input wire:model="remember" id="remember_me" type="checkbox" class="custom-checkbox">
                <span class="checkbox-label">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="primary-button" wire:loading.attr="disabled">
            <span wire:loading.remove>Log in</span>
            <span wire:loading>Authenticating...</span>
            <span style="font-size: 1.2rem;" wire:loading.remove>→</span>
            <span wire:loading class="animate-spin" style="font-size: 1rem;">↻</span>
        </button>
    </form>

    @if (Route::has('register'))
        <div class="divider"></div>

        <div class="text-center">
            <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" wire:navigate class="auth-link" style="margin-left: 0.25rem;">
                    {{ __('Create an account') }}
                </a>
            </p>
        </div>
    @endif
</div>