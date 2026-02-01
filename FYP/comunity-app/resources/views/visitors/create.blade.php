@extends('layouts.app')

@section('title', 'Register Visitor')

@section('content')
    <div class="visitor-create" style="max-width: 600px; margin: 0 auto;">
        <div class="page-header" style="margin-bottom: 2rem;">
            <a href="{{ route('visitors.index') }}"
                style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to List
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 2rem; color: var(--text-primary);">Register Visitor</h1>
            <p style="color: var(--text-secondary);">Fill in the details for your upcoming guest.</p>
        </div>

        <div class="glass-card" style="padding: 2rem;">
            <form action="{{ route('visitors.store') }}" method="POST"
                style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf

                <div class="form-group">
                    <label for="name"
                        style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Visitor
                        Name</label>
                    <input type="text" name="name" id="name" required class="glass-input"
                        style="width: 100%; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem 1rem; color: white;"
                        placeholder="Enter full name">
                    @error('name') <span style="color: #f5576c; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="expected_arrival"
                        style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Expected
                        Arrival</label>
                    <input type="datetime-local" name="expected_arrival" id="expected_arrival" required class="glass-input"
                        style="width: 100%; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem 1rem; color: white;">
                    @error('expected_arrival') <span style="color: #f5576c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="vehicle_number"
                        style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Vehicle
                        Plate Number (Optional)</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" class="glass-input"
                        style="width: 100%; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem 1rem; color: white;"
                        placeholder="e.g. ABC 1234">
                    @error('vehicle_number') <span style="color: #f5576c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="visit_purpose"
                        style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Purpose
                        of Visit</label>
                    <select name="visit_purpose" id="visit_purpose" required class="glass-input"
                        style="width: 100%; border-radius: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); padding: 0.75rem 1rem; color: white;">
                        <option value="" disabled selected style="background: #1a1a3a;">Select purpose</option>
                        <option value="Social Visit" style="background: #1a1a3a;">Social Visit</option>
                        <option value="Delivery" style="background: #1a1a3a;">Delivery</option>
                        <option value="Maintenance / Service" style="background: #1a1a3a;">Maintenance / Service</option>
                        <option value="Event / Party" style="background: #1a1a3a;">Event / Party</option>
                        <option value="Other" style="background: #1a1a3a;">Other</option>
                    </select>
                    @error('visit_purpose') <span style="color: #f5576c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary"
                        style="width: 100%; padding: 1rem; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; border: none; font-size: 1rem;">
                        Generate Visitor Pass
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection