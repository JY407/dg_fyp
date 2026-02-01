@extends('layouts.app')

@section('title', 'Visitor Management')

@section('content')
    <div class="visitor-index">
        <div class="page-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-family: var(--font-heading); font-size: 2rem; color: var(--text-primary);">Visitor
                    Management</h1>
                <p style="color: var(--text-secondary);">Manage and track your community visitors.</p>
            </div>
            <a href="{{ route('visitors.create') }}" class="btn btn-primary"
                style="padding: 0.75rem 1.5rem; text-decoration: none; border-radius: var(--radius-md); font-weight: 600;">
                Register Visitor
            </a>
        </div>

        @if(session('success'))
            <div class="glass-card"
                style="padding: 1rem; margin-bottom: 1.5rem; border-left: 4px solid #00f2fe; color: var(--text-primary);">
                {{ session('success') }}
            </div>
        @endif

        <div class="visitors-grid"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @forelse($visitors as $visitor)
                <div class="glass-card visitor-card"
                    style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; position: relative; overflow: hidden;">
                    <div class="status-badge"
                        style="position: absolute; top: 1rem; right: 1rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border);">
                        {{ $visitor->status }}
                    </div>

                    <div class="visitor-info">
                        <h3 style="margin-bottom: 0.25rem; font-size: 1.1rem; color: var(--text-primary);">{{ $visitor->name }}
                        </h3>
                        <p
                            style="font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ $visitor->expected_arrival->format('M d, Y - h:i A') }}
                        </p>
                    </div>

                    <div style="height: 1px; background: var(--glass-border);"></div>

                    <div class="visitor-details"
                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem;">
                        <div>
                            <span style="color: var(--text-secondary); display: block;">Vehicle</span>
                            <span style="color: var(--text-primary);">{{ $visitor->vehicle_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); display: block;">Purpose</span>
                            <span style="color: var(--text-primary);">{{ $visitor->visit_purpose }}</span>
                        </div>
                    </div>

                    <div class="visitor-actions" style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                        <a href="{{ route('visitors.show', $visitor) }}" class="btn btn-ghost"
                            style="flex: 1; text-align: center; text-decoration: none; padding: 0.5rem; border-radius: 8px; font-size: 0.85rem;">
                            View Pass
                        </a>
                        <form action="{{ route('visitors.destroy', $visitor) }}" method="POST" style="flex: 1;"
                            onsubmit="return confirm('Are you sure you want to cancel this visit?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost"
                                style="width: 100%; color: #f5576c; padding: 0.5rem; border-radius: 8px; font-size: 0.85rem; background: transparent; border: none; cursor: pointer;">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="glass-card" style="grid-column: 1 / -1; padding: 3rem; text-align: center;">
                    <div style="margin-bottom: 1rem; color: var(--text-secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 style="color: var(--text-primary);">No visitors registered</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">You haven't added any upcoming visitors yet.
                    </p>
                    <a href="{{ route('visitors.create') }}" class="btn btn-primary"
                        style="padding: 0.75rem 1.5rem; text-decoration: none; border-radius: var(--radius-md); font-weight: 600;">
                        Register Your First Visitor
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection