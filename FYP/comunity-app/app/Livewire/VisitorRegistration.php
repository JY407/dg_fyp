<?php

namespace App\Livewire;

use App\Models\Visitor;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class VisitorRegistration extends Component
{
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

    /**
     * Register a new visitor
     */
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

    /**
     * Generate a unique pass code
     */
    private function generatePassCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.visitor-registration');
    }
}
