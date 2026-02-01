<?php

use App\Models\Visitor;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.app')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public string $ic_number = '';

    #[Validate('nullable|string|max:20')]
    public string $vehicle_number = '';

    #[Validate('required|string|max:255')]
    public string $visit_purpose = '';

    #[Validate('nullable|numeric|between:-90,90')]
    public ?float $latitude = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public ?float $longitude = null;

    #[Validate('nullable|string')]
    public ?string $location_address = null;

    public bool $locationCaptured = false;

    public function register()
    {
        \Log::info('Visitor registration attempt', [
            'name' => $this->name,
            'has_location' => $this->latitude !== null
        ]);

        $validated = $this->validate();

        // Add location captured timestamp if location was provided
        if ($this->latitude && $this->longitude) {
            $validated['location_captured_at'] = now();
        }

        $validated['user_id'] = auth()->id();
        $validated['pass_code'] = $this->generatePassCode();
        $validated['expected_arrival'] = now()->addHours(1);
        $validated['status'] = 'pending';
        $validated['ip_address'] = request()->ip();

        $visitor = Visitor::create($validated);

        \Log::info('Visitor created', ['visitor_id' => $visitor->id]);

        session()->flash('success', 'Visitor registered successfully! Pass Code: ' . $visitor->pass_code);

        $this->reset();
    }

    private function generatePassCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }
};
?>

<div class="max-w-4xl mx-auto p-6">

    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">📝 Register Visitor</h2>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Location Status -->
            <div x-data="{ 
            locationStatus: 'checking',
            accuracy: null
        }" x-init="
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        $wire.set('latitude', position.coords.latitude);
                        $wire.set('longitude', position.coords.longitude);
                        $wire.set('locationCaptured', true);
                        accuracy = Math.round(position.coords.accuracy);
                        locationStatus = 'captured';
                        
                        // Optional: Reverse geocode to get address
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json`)
                            .then(res => res.json())
                            .then(data => {
                                $wire.set('location_address', data.display_name);
                            })
                            .catch(err => console.log('Geocoding failed:', err));
                    },
                    (error) => {
                        locationStatus = 'denied';
                        console.log('Location access denied:', error);
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                locationStatus = 'unavailable';
            }
        " class="mb-6">
                <div x-show="locationStatus === 'checking'"
                    class="p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                    📍 Checking location...
                </div>
                <div x-show="locationStatus === 'captured'"
                    class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span>✅ Location captured</span>
                        <span x-show="accuracy" class="text-sm" x-text="'Accuracy: ~' + accuracy + 'm'"></span>
                    </div>
                </div>
                <div x-show="locationStatus === 'denied'"
                    class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                    ⚠️ Location access denied (registration will still work)
                </div>
                <div x-show="locationStatus === 'unavailable'"
                    class="p-4 bg-gray-100 border border-gray-400 text-gray-700 rounded-lg">
                    ℹ️ Location not available on this device
                </div>
            </div>

            <form wire:submit="register" class="space-y-6">
                <!-- Visitor Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Visitor Name <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="name" id="name" type="text" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="John Doe">
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- IC Number -->
                <div>
                    <label for="ic_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        IC Number (Optional)
                    </label>
                    <input wire:model="ic_number" id="ic_number" type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="123456-78-9012">
                    @error('ic_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Vehicle Number -->
                <div>
                    <label for="vehicle_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Vehicle Number (Optional)
                    </label>
                    <input wire:model="vehicle_number" id="vehicle_number" type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="ABC 1234">
                    @error('vehicle_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Visit Purpose -->
                <div>
                    <label for="visit_purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Visit Purpose <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="visit_purpose" id="visit_purpose" type="text" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Visiting family, delivery, etc.">
                    @error('visit_purpose')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Display captured location -->
                @if($latitude && $longitude)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">📍 Captured Location</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Coordinates:</strong> {{ number_format($latitude, 6) }},
                            {{ number_format($longitude, 6) }}
                        </p>
                        @if($location_address)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $location_address }}
                            </p>
                        @endif
                        <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank"
                            class="text-purple-600 hover:text-purple-800 text-sm mt-2 inline-block">
                            View on Google Maps →
                        </a>
                    </div>
                @endif

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Register Visitor</span>
                    <span wire:loading>Registering...</span>
                </button>
            </form>
        </div>
    </div>