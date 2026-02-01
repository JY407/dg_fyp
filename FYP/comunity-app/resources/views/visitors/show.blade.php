@extends('layouts.app')

@section('title', 'Visitor Pass')

@section('content')
    <div class="visitor-show" style="max-width: 500px; margin: 0 auto;">
        <div class="page-header" style="margin-bottom: 2rem; text-align: center;">
            <h1 style="font-family: var(--font-heading); font-size: 2rem; color: var(--text-primary);">Visitor Pass</h1>
            <p style="color: var(--text-secondary);">Share this pass with your visitor for entry.</p>
        </div>

        <div class="glass-card visitor-pass" style="padding: 2.5rem; text-align: center; position: relative;">
            @if(session('success'))
                <div style="color: #00f2fe; margin-bottom: 1rem; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="pass-id"
                style="font-size: 2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem; letter-spacing: 5px;">
                {{ $visitor->pass_code }}
            </div>

            <div class="qr-mock"
                style="width: 200px; height: 200px; margin: 0 auto 2rem; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 1rem;">
                {{-- Mock QR Code UI --}}
                <div
                    style="width: 100%; height: 100%; background: repeating-conic-gradient(#000 0% 25%, #fff 0% 50%) 50% / 10px 10px;">
                </div>
            </div>

            <div
                style="text-align: left; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <div style="margin-bottom: 0.75rem;">
                    <span style="color: var(--text-secondary); font-size: 0.8rem; display: block;">Visitor</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ $visitor->name }}</span>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <span style="color: var(--text-secondary); font-size: 0.8rem; display: block;">Arriving</span>
                    <span
                        style="color: var(--text-primary); font-weight: 600;">{{ $visitor->expected_arrival->format('d M Y, h:i A') }}</span>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <span style="color: var(--text-secondary); font-size: 0.8rem; display: block;">Vehicle</span>
                    <span
                        style="color: var(--text-primary); font-weight: 600;">{{ $visitor->vehicle_number ?? 'No vehicle' }}</span>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.8rem; display: block;">Host</span>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ $visitor->user->name }}</span>
                </div>
            </div>

            <p style="font-size: 0.8rem; color: var(--text-secondary);">Present this QR code or Pass ID at the security gate
                for entry.</p>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <a href="{{ route('visitors.index') }}" class="btn btn-ghost"
                style="flex: 1; text-align: center; text-decoration: none; padding: 0.75rem; border-radius: 8px;">
                Back to List
            </a>
            <button onclick="window.print()" class="btn btn-primary"
                style="flex: 1; padding: 0.75rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">
                Download / Print
            </button>
        </div>
    </div>
@endsection