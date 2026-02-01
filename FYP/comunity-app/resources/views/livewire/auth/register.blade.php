<div class="glass-card">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Join your community today</p>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input wire:model="name" id="name" type="text" class="form-input" required autofocus autocomplete="name"
                placeholder="John Doe">
            @error('name') <span class="error-message">⚠️ {{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input wire:model="email" id="email" type="email" class="form-input" required autocomplete="email"
                placeholder="john@example.com">
            @error('email') <span class="error-message">⚠️ {{ $message }}</span> @enderror
        </div>

        <!-- Unit Info Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Unit Number -->
            <div class="form-group">
                <label for="unit_number" class="form-label">Unit Number</label>
                <input wire:model="unit_number" id="unit_number" type="text" class="form-input" required
                    placeholder="#01-01">
                @error('unit_number') <span class="error-message">⚠️ {{ $message }}</span> @enderror
            </div>

            <!-- User Type -->
            <div class="form-group">
                <label for="user_type" class="form-label">I am a...</label>
                <select wire:model="user_type" id="user_type" class="form-input" required
                    style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FFF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto; padding-right: 2.5rem;">
                    <option value="" disabled selected>Select Type</option>
                    <option value="owner" style="color: black;">Owner</option>
                    <option value="tenant" style="color: black;">Tenant</option>
                </select>
                @error('user_type') <span class="error-message">⚠️ {{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Address Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Block -->
            <div class="form-group">
                <label for="block" class="form-label">Block (Optional)</label>
                <input wire:model="block" id="block" type="text" class="form-input" placeholder="Block A">
            </div>

            <!-- Street -->
            <div class="form-group">
                <label for="street" class="form-label">Street (Optional)</label>
                <input wire:model="street" id="street" type="text" class="form-input" placeholder="Main St">
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input wire:model="password" id="password" type="password" class="form-input" required
                autocomplete="new-password" placeholder="••••••••">
            @error('password') <span class="error-message">⚠️ {{ $message }}</span> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" class="form-input"
                required autocomplete="new-password" placeholder="••••••••">
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="primary-button" wire:loading.attr="disabled">
                <span wire:loading.remove>Create Account</span>
                <span wire:loading>Creating Account...</span>
                <span style="font-size: 1.2rem;" wire:loading.remove>→</span>
                <span wire:loading class="animate-spin" style="font-size: 1rem;">↻</span>
            </button>
        </div>
    </form>

    <div class="divider"></div>

    <div class="text-center">
        <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem;">
            {{ __("Already have an account?") }}
            <a href="{{ route('login') }}" wire:navigate class="auth-link" style="margin-left: 0.25rem;">
                {{ __('Log in') }}
            </a>
        </p>
    </div>
</div>