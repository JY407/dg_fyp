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

<div class="dashboard-container">
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .management-grid {
                display: grid;
                grid-template-columns: 1fr 1.5fr;
                gap: 2rem;
                align-items: start;
            }

            @media (max-width: 1024px) {
                .management-grid {
                    grid-template-columns: 1fr;
                }
            }

            #map {
                height: 600px;
                width: 100%;
                border-radius: 0.75rem;
                z-index: 1;
            }

            .form-card {
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .map-card {
                background: white;
                padding: 1rem;
                border-radius: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                height: 100%;
            }

            .generated-pass {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                padding: 1.5rem;
                border-radius: 0.75rem;
                margin-bottom: 2rem;
                text-align: center;
                animation: slideDown 0.3s ease-out;
            }

            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endpush

    <div class="page-header">
        <h1 class="page-title">Visitor Management</h1>
        <p class="page-subtitle">Register new visitors and monitor active guests in real-time.</p>
    </div>

    <div class="management-grid">
        <!-- LEFT COLUMN: Registration Form -->
        <div class="form-card">
            <h2 class="card-title" style="margin-bottom: 1.5rem;">📝 Register New Visitor</h2>

            @if (session('success'))
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if ($generatedPassUrl)
                <div class="generated-pass">
                    <h3 style="color: #1e40af; font-weight: 600; margin-bottom: 0.5rem;">Visitor Pass Generated</h3>
                    <p style="color: #60a5fa; margin-bottom: 1rem;">Share this link to verify location:</p>
                    
                    <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                        <input type="text" readonly value="{{ $generatedPassUrl }}" 
                            style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white;"
                            id="passUrlInput">
                        <button onclick="copyUrl()" 
                            style="background: #2563eb; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 500;">
                            Copy
                        </button>
                    </div>
                </div>
            @endif

            <form wire:submit="register" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Visitor Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input wire:model="name" type="text" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;"
                        placeholder="John Doe">
                    @error('name') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                            IC Number
                        </label>
                        <input wire:model="ic_number" type="text"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;"
                            placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                            Vehicle No.
                        </label>
                        <input wire:model="vehicle_number" type="text"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;"
                            placeholder="Optional">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Visit Purpose <span style="color: #ef4444;">*</span>
                    </label>
                    <input wire:model="visit_purpose" type="text" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;"
                        placeholder="Reference (e.g. Delivery)">
                    @error('visit_purpose') <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    style="background: black; color: white; padding: 1rem; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; margin-top: 1rem; width: 100%;">
                    <span wire:loading.remove>Generate Pass & Track</span>
                    <span wire:loading>Processing...</span>
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: Map Monitoring -->
        <div class="map-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 class="card-title">📍 Live User Tracking</h2>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="badge badge-primary" wire:poll.5s>
                        {{ count($visitors) }} Active
                    </span>
                </div>
            </div>
            
            <div id="map" wire:ignore></div>
        </div>
    </div>
    
    {{-- Polling to refresh data --}}
    <div wire:poll.5s>
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
                            let popupContent = `<b>${visitor.name}</b><br>`;
                            
                            if (visitor.is_checked_in) {
                                popupContent += `<span style="color: green; font-weight: bold;">✔ Verified</span><br>`;
                                if (visitor.address) {
                                    popupContent += `<small>${visitor.address}</small><br>`;
                                }
                            } else {
                                popupContent += `<button onclick="checkInVisitor(${visitor.id})" 
                                    style="margin-top:5px; background:blue; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">
                                    Verify Location
                                </button><br>`;
                            }
                            popupContent += `<small style="color:gray;">Updated: ${visitor.last_update}</small>`;

                            if (markers[visitor.id]) {
                                markers[visitor.id].setLatLng([visitor.lat, visitor.lng]);
                                markers[visitor.id].setPopupContent(popupContent);
                            } else {
                                markers[visitor.id] = L.marker([visitor.lat, visitor.lng])
                                    .addTo(map)
                                    .bindPopup(popupContent);
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