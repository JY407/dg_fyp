<?php

use App\Models\Visitor;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.admin')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public string $ic_number = '';

    #[Validate('nullable|string|max:20')]
    public string $vehicle_number = '';

    #[Validate('required|string|max:255')]
    public string $visit_purpose = '';

    public ?string $generatedPassUrl = null;
    public ?string $generatedPassCode = null;

    public function register()
    {
        $validated = $this->validate();

        $validated['user_id'] = auth()->id();
        $validated['pass_code'] = $this->generatePassCode();
        $validated['expected_arrival'] = now();
        $validated['status'] = 'approved';

        $visitor = Visitor::create($validated);

        $this->generatedPassCode = $visitor->pass_code;
        $this->generatedPassUrl = route('visitor.pass', ['passCode' => $visitor->pass_code]);

        session()->flash('success', 'Visitor pass created successfully!');

        $this->name = '';
        $this->ic_number = '';
        $this->vehicle_number = '';
        $this->visit_purpose = '';

        // Refresh valid visitors list
        $this->dispatch('visitors-updated', $this->with()['visitors']);
    }

    private function generatePassCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public function checkIn($visitorId)
    {
        $visitor = Visitor::find($visitorId);
        if ($visitor) {
            $visitor->update([
                'status' => 'checked_in',
                'check_in_time' => now(),
            ]);
            $this->dispatch('visitors-updated', $this->with()['visitors']);
        }
    }

    public function with()
    {
        $visitors = Visitor::whereNotNull('latitude')
            ->with([
                'locations' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->get()
            ->map(function ($visitor) {
                $location = $visitor->locations->first();
                $lat = $location ? $location->latitude : $visitor->latitude;
                $lng = $location ? $location->longitude : $visitor->longitude;

                return [
                    'id' => $visitor->id,
                    'name' => $visitor->name,
                    'lat' => $lat,
                    'lng' => $lng,
                    'address' => $visitor->location_address,
                    'is_checked_in' => $visitor->status === 'checked_in' || $visitor->check_in_time !== null,
                    'last_update' => $location ? $location->created_at->diffForHumans() : 'Just now',
                ];
            });

        return [
            'visitors' => $visitors,
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto space-y-6">
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .leaflet-pane {
                z-index: 10;
            }

            .leaflet-top,
            .leaflet-bottom {
                z-index: 20;
            }

            /* Custom Map Styling for Glassmorphism Context */
            .leaflet-container {
                font-family: var(--font-primary);
                border-radius: var(--radius-lg);
            }

            .leaflet-popup-content-wrapper {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: var(--radius-md);
            }

            .leaflet-popup-tip {
                background: rgba(255, 255, 255, 0.9);
            }
        </style>
    @endpush

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1>Visitor Management</h1>
            <p>Register new visitors and monitor active guests in real-time.</p>
        </div>
        <div>
            <span class="badge badge-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                <span class="d-inline-block rounded-full bg-white mr-2"
                    style="width: 8px; height: 8px; animation: pulse 2s infinite;"></span>
                <span wire:poll.5s>{{ count($visitors) }} Active Visitors</span>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- LEFT COLUMN: Registration Form -->
        <div class="lg:col-span-4">
            <div class="glass-card">
                <div class="mb-4 flex items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%;">
                        📝
                    </div>
                    <h2 style="margin: 0; font-size: 1.5rem;">Register Visitor</h2>
                </div>

                @if (session('success'))
                    <div class="glass-card glass-card-sm mb-4"
                        style="background: rgba(0, 242, 254, 0.1); border-color: var(--success-color);">
                        <div class="flex items-center gap-2 text-success" style="color: #00f2fe;">
                            ✅ <span class="font-bold">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if ($generatedPassUrl)
                    <div class="glass-card glass-card-sm mb-4 animate-fade-in"
                        style="background: rgba(102, 126, 234, 0.1);">
                        <h4 class="text-center mb-2" style="color: var(--primary-color);">Visitor Pass Generated</h4>
                        <p class="text-center text-sm mb-3">Share this link to verify location</p>

                        <div class="flex gap-2">
                            <input type="text" readonly value="{{ $generatedPassUrl }}" class="form-input text-sm"
                                id="passUrlInput">
                            <button onclick="copyUrl()" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                Copy
                            </button>
                        </div>
                    </div>
                @endif

                <form wire:submit="register">
                    <div class="form-group">
                        <label class="form-label">Visitor Name <span
                                style="color: var(--accent-color);">*</span></label>
                        <input wire:model="name" type="text" required class="form-input" placeholder="e.g. John Doe">
                        @error('name') <span class="text-accent text-sm mt-1 d-block"
                        style="color: var(--accent-color);">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-2 gap-4 mb-3">
                        <div class="form-group mb-0">
                            <label class="form-label">IC Number</label>
                            <input wire:model="ic_number" type="text" class="form-input" placeholder="Optional">
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Vehicle No.</label>
                            <input wire:model="vehicle_number" type="text" class="form-input" placeholder="Optional">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Visit Purpose <span
                                style="color: var(--accent-color);">*</span></label>
                        <input wire:model="visit_purpose" type="text" required class="form-input"
                            placeholder="e.g. Delivery, Contractor">
                        @error('visit_purpose') <span class="text-accent text-sm mt-1 d-block"
                        style="color: var(--accent-color);">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary w-full mt-2"
                        style="width: 100%;">
                        <span wire:loading.remove>Generate Pass & Track</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Map Monitoring -->
        <div class="lg:col-span-8">
            <div class="glass-card p-1" style="height: 600px;">
                <div id="map" wire:ignore style="height: 100%; width: 100%; border-radius: var(--radius-lg);"></div>
            </div>
        </div>
    </div>

    {{-- Polling to refresh data --}}
    <div wire:poll.5s class="d-none">
        @php
            $this->dispatch('visitors-updated', $visitors);
        @endphp
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            function copyUrl() {
                var copyText = document.getElementById("passUrlInput");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
                alert("Pass URL copied!");
            }

            document.addEventListener('livewire:initialized', () => {
                const map = L.map('map').setView([1.3521, 103.8198], 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                let markers = {};

                const updateMarkers = (visitors) => {
                    console.log('Updating markers:', visitors);
                    const visitorIds = visitors.map(v => v.id);

                    // Cleanup old markers
                    for (const id in markers) {
                        if (!visitorIds.includes(parseInt(id))) {
                            map.removeLayer(markers[id]);
                            delete markers[id];
                        }
                    }

                    // Add/Update markers
                    visitors.forEach(visitor => {
                        if (visitor.lat && visitor.lng) {
                            // Custom Popup Styling
                            let popupContent = `
                                    <div style="font-family: 'Inter', sans-serif; color: #333;">
                                        <div style="font-weight: 700; font-size: 1rem; margin-bottom: 4px;">${visitor.name}</div>
                                `;

                            if (visitor.is_checked_in) {
                                popupContent += `
                                        <div style="color: #059669; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">
                                            ✔ Verified
                                        </div>
                                    `;
                                if (visitor.address) {
                                    popupContent += `<div style="color: #666; font-size: 0.75rem; margin-bottom: 4px;">${visitor.address}</div>`;
                                }
                            } else {
                                popupContent += `
                                        <button onclick="checkInVisitor(${visitor.id})" 
                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.75rem; font-weight: 600; width: 100%; margin-top: 4px;">
                                            Verify Location
                                        </button>
                                    `;
                            }
                            popupContent += `<div style="color: #999; font-size: 0.7rem; margin-top: 6px; border-top: 1px solid #eee; padding-top: 4px;">Updated: ${visitor.last_update}</div></div>`;

                            if (markers[visitor.id]) {
                                markers[visitor.id].setLatLng([visitor.lat, visitor.lng]);
                                markers[visitor.id].setPopupContent(popupContent);
                            } else {
                                markers[visitor.id] = L.marker([visitor.lat, visitor.lng])
                                    .addTo(map)
                                    .bindPopup(popupContent, { minWidth: 200 });
                            }
                        }
                    });
                };

                window.checkInVisitor = (id) => {
                    @this.call('checkIn', id);
                };

                updateMarkers(@json($visitors));

                Livewire.on('visitors-updated', (data) => {
                    updateMarkers(data[0]);
                });
            });
        </script>
    @endpush
</div>