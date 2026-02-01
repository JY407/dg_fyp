<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Visitor;

new #[Layout('layouts.admin')] class extends Component {
    public function resolveAlert($id)
    {
        $alert = \App\Models\EmergencyAlert::find($id);
        if ($alert) {
            $alert->update(['status' => 'resolved']);
        }
    }

    public function with()
    {
        $stats = [
            'total_users' => \App\Models\User::where('user_type', '!=', 'admin')->count(),
            'total_announcements' => \App\Models\Announcement::count(),
            'recent_activity' => Visitor::latest()->take(5)->get(),
            'active_visitors' => Visitor::whereNotNull('latitude')->count(),
        ];

        return [
            'stats' => $stats,
            'emergency_alerts' => \App\Models\EmergencyAlert::with('user')->where('status', 'pending')->latest()->get(),
        ];
    }
}; ?>

<div class="dashboard-container">
    @push('styles')
        <style>
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .stat-card {
                background: white;
                padding: 1.5rem;
                border-radius: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                display: flex;
                flex-direction: column;
            }

            .stat-title {
                color: #6b7280;
                font-size: 0.875rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
            }

            .stat-value {
                color: #111827;
                font-size: 1.875rem;
                font-weight: 700;
            }

            .activity-list {
                list-style: none;
                padding: 0;
            }

            .activity-item {
                display: flex;
                align-items: center;
                padding: 0.75rem 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-avatar {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                background: #ebf5ff;
                color: #3b82f6;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                margin-right: 1rem;
            }

            .activity-content h4 {
                font-size: 0.9rem;
                font-weight: 600;
                margin: 0;
            }

            .activity-content p {
                font-size: 0.8rem;
                color: #6b7280;
                margin: 0;
            }
        </style>
    @endpush

    <div class="page-header">
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <!-- Emergency Alerts Section -->
    @if($emergency_alerts->isNotEmpty())
        <div class="alert-section"
            style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; animation: pulse 2s infinite;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="color: #b91c1c; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    ⚠️ EMERGENCY ALERTS ACTIVE ({{ $emergency_alerts->count() }})
                </h2>
            </div>

            <div style="display: grid; gap: 1rem;">
                @foreach($emergency_alerts as $alert)
                    <div
                        style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #ef4444; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0 0 0.25rem 0; color: #1f2937;">
                                {{ $alert->user->name }}
                                <span
                                    style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; margin-left: 0.5rem;">
                                    Unit: {{ $alert->user->unit_number ?? 'N/A' }} | Block: {{ $alert->user->block ?? 'N/A' }}
                                </span>
                            </h3>
                            <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">
                                Time: {{ $alert->created_at->format('d M Y, h:i A') }}
                                ({{ $alert->created_at->diffForHumans() }})
                            </p>
                        </div>
                        <button wire:click="resolveAlert({{ $alert->id }})"
                            wire:confirm="Are you sure you want to resolve this alert?"
                            style="background: #dc2626; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
                            Resolve
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        <style>
            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
                }
            }
        </style>
    @endif

    <!-- Stats Row -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-title">Total Users</span>
            <span class="stat-value">{{ $stats['total_users'] }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Total Announcements</span>
            <span class="stat-value">{{ $stats['total_announcements'] }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Active Visitors</span>
            <span class="stat-value">{{ $stats['active_visitors'] }}</span>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card">
        <h2 class="card-title" style="margin-bottom: 1rem;">Recent Activity</h2>
        <ul class="activity-list">
            @forelse($stats['recent_activity'] as $activity)
                <li class="activity-item">
                    <div class="activity-avatar">
                        {{ substr($activity->name, 0, 1) }}
                    </div>
                    <div class="activity-content">
                        <h4>{{ $activity->name }}</h4>
                        <p>Checked in {{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </li>
            @empty
                <li style="color: #6b7280; text-align: center; padding: 1rem;">No recent activity</li>
            @endforelse
        </ul>
    </div>
</div>