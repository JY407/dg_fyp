<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Visitor;
use App\Models\VisitorLocation;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.auth.simple', ['title' => 'Visitor Pass'])]
class VisitorPass extends Component
{
    public $passCode;
    public Visitor $visitor;
    public $trackingActive = false;

    public function mount($passCode)
    {
        $this->passCode = $passCode;
        $this->visitor = Visitor::where('pass_code', $passCode)->firstOrFail();
    }

    public function updateLocation($latitude, $longitude, $address = null)
    {
        // Save new location point
        VisitorLocation::create([
            'visitor_id' => $this->visitor->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        // Also update the main visitor record for quick access
        $this->visitor->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_address' => $address,
            'location_captured_at' => now(),
            'check_in_time' => $this->visitor->check_in_time ?? now(), // Ensure check-in time is set
        ]);

        $this->trackingActive = true;
    }

    public function render()
    {
        return view('livewire.visitor-pass');
    }
}
