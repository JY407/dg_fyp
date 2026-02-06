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
                        <div class="event-month">Feb</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Batik Painting Class</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 10:00 AM - 1:00 PM</div>
                            <div class="event-info-item">📍 Community Center</div>
                        </div>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">
                        <div class="event-day">22</div>
                        <div class="event-month">Feb</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Traditional Wedding Showcase</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 2:00 PM - 5:00 PM</div>
                            <div class="event-info-item">📍 Main Hall</div>
                        </div>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">
                        <div class="event-day">28</div>
                        <div class="event-month">Feb</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">Cultural Food Fair</h3>
                        <div class="event-info">
                            <div class="event-info-item">⏰ 9:00 AM - 4:00 PM</div>
                            <div class="event-info-item">📍 Town Square</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection