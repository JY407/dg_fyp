@extends('layouts.app')

@section('title', 'Events - Community Connect')

@section('content')
    <section style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <h1>Community Events</h1>
            <p class="text-secondary" style="margin-bottom: 3rem;">Discover and join upcoming community events</p>

            <div class="grid grid-3">
                <div class="event-card">
                    <div class="event-date">
                        <div class="event-day">15</div>
                        <div class="event-month">Jan</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Community Cleanup Day</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 9:00 AM - 12:00 PM</div>
                            <div class="event-info-item">📍 Community Park</div>
                        </div>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">
                        <div class="event-day">22</div>
                        <div class="event-month">Jan</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Movie Night</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 7:00 PM - 10:00 PM</div>
                            <div class="event-info-item">📍 Community Center</div>
                        </div>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">
                        <div class="event-day">28</div>
                        <div class="event-month">Jan</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Farmers Market</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 8:00 AM - 2:00 PM</div>
                            <div class="event-info-item">📍 Main Street</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection