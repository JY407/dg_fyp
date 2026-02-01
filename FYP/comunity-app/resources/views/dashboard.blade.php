@extends('layouts.app')

@section('title', 'Dashboard - Community Connect')

@section('content')
    <section style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <h1>Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-secondary">Manage your community activities and stay connected with your neighbors.</p>

            <div class="grid grid-3" style="margin-top: 2rem;">
                <div class="glass-card">
                    <h3>Quick Stats</h3>
                    <p class="text-secondary">View your community activity</p>
                </div>

                <div class="glass-card">
                    <h3>Recent Activity</h3>
                    <p class="text-secondary">Latest updates from your community</p>
                </div>

                <div class="glass-card">
                    <h3>Upcoming Events</h3>
                    <p class="text-secondary">Don't miss out on community events</p>
                </div>
            </div>
        </div>
    </section>
@endsection