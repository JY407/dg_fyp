<div class="glass-card" style="max-width: 500px; margin: 0 auto; text-align: center;">
    <div style="margin-bottom: 2rem;">
        <h2 class="auth-title">Visitor E-Pass</h2>
        <p class="auth-subtitle">Keep this page open while inside the premises.</p>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; display: inline-block;">
        {{-- In a real app, this would be a QR Code component --}}
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $visitor->pass_code }}" alt="QR Code"
            style="width: 200px; height: 200px; display: block;">
        <div
            style="color: black; font-weight: bold; font-family: monospace; font-size: 1.5rem; margin-top: 1rem; letter-spacing: 2px;">
            {{ $visitor->pass_code }}
        </div>
    </div>

    <div class="space-y-4 text-left"
        style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
        <div>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Visitor Name</span>
            <div style="font-weight: 600; font-size: 1.1rem;">{{ $visitor->name }}</div>
        </div>
        <div>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Purpose</span>
            <div style="font-weight: 600;">{{ $visitor->visit_purpose }}</div>
        </div>
        <div>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Valid Until</span>
            <div style="font-weight: 600;">{{ $visitor->expected_arrival->addHours(4)->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- Live Tracking Script --}}
    <div x-data="{
        status: 'initializing',
        lastUpdated: null,
        error: null,
        interval: null,
        
        startTracking() {
            this.status = 'requesting';
            
            if (!navigator.geolocation) {
                this.status = 'unsupported';
                return;
            }

            // Request immediate position first
            navigator.geolocation.getCurrentPosition(
                (pos) => this.sendPosition(pos),
                (err) => this.handleError(err)
            );

            // Then watch for changes (more efficient for movement)
            this.interval = navigator.geolocation.watchPosition(
                (pos) => this.sendPosition(pos),
                (err) => this.handleError(err),
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        },

        sendPosition(position) {
            this.status = 'tracking';
            this.lastUpdated = new Date().toLocaleTimeString();
            this.error = null;
            
            // Call Livewire method
            $wire.updateLocation(
                position.coords.latitude,
                position.coords.longitude
            );
        },

        handleError(error) {
            console.error(error);
            if (error.code === 1) { // PERMISSION_DENIED
                this.status = 'denied';
            } else {
                this.error = error.message;
            }
        }
    }" x-init="startTracking()" class="location-status">

        <!-- Status Indicators -->
        <template x-if="status === 'tracking'">
            <div
                style="padding: 1rem; background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 12px; color: #34d399;">
                <div
                    style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="animate-pulse"
                        style="display: inline-block; width: 10px; height: 10px; background: #34d399; border-radius: 50%;"></span>
                    <span style="font-weight: 600;">Live Tracking Active</span>
                </div>
                <div style="font-size: 0.85rem; opacity: 0.8;">
                    Last updated: <span x-text="lastUpdated"></span>
                </div>
            </div>
        </template>

        <template x-if="status === 'denied'">
            <div
                style="padding: 1rem; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 12px; color: #fca5a5;">
                <strong>⚠️ Location Access Required</strong>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">Please release location permission in your browser
                    settings to valid this pass.</p>
                <button @click="startTracking()" class="primary-button"
                    style="margin-top: 1rem; font-size: 0.9rem; padding: 0.5rem 1rem;">
                    Retry Permission
                </button>
            </div>
        </template>

        <template x-if="status === 'requesting'">
            <div style="padding: 1rem; color: rgba(255,255,255,0.6);">
                Requesting location access...
            </div>
        </template>
    </div>
</div>