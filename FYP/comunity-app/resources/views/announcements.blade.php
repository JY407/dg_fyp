@extends('layouts.app')

@section('title', 'Announcements - Community Connect')

@section('content')
    <section style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <h1>Community Announcements</h1>
            <p class="text-secondary" style="margin-bottom: 3rem;">Stay updated with the latest news from your community</p>

            <div class="grid grid-2">
                <div class="glass-card">
                    <div class="flex-between" style="margin-bottom: 1rem;">
                        <span class="badge badge-primary">Important</span>
                        <span class="text-muted" style="font-size: 0.875rem;">2 hours ago</span>
                    </div>
                    <h3>Community Meeting This Weekend</h3>
                    <p class="text-secondary">Join us for our monthly community meeting to discuss upcoming projects and
                        initiatives.</p>
                </div>

                <div class="glass-card">
                    <div class="flex-between" style="margin-bottom: 1rem;">
                        <span class="badge badge-secondary">Update</span>
                        <span class="text-muted" style="font-size: 0.875rem;">1 day ago</span>
                    </div>
                    <h3>New Playground Equipment Installed</h3>
                    <p class="text-secondary">The community playground has been upgraded with new equipment for all ages.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection