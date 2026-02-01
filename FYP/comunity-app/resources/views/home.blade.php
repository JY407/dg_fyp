@extends('layouts.app')

@section('title', 'Community Connect - Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero" style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <div class="hero-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h1 class="animate-fade-in" style="margin-bottom: 1.5rem;">
                    Connect, Communicate, Community
                </h1>
                <p class="animate-fade-in"
                    style="font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 2rem; animation-delay: 0.2s;">
                    Your central hub for community engagement, announcements, events, and meaningful conversations. Stay
                    connected with your neighbors and build a stronger community together.
                </p>
                <div class="hero-buttons" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Get Started</a>
                    <a href="#features" class="btn btn-outline">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" style="padding: 80px 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2>Everything You Need</h2>
                <p class="text-secondary" style="font-size: 1.125rem; max-width: 600px; margin: 0 auto;">
                    Powerful features designed to bring your community closer together
                </p>
            </div>

            <div class="grid grid-3">
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">📢</div>
                    <h3 class="feature-title">Announcements</h3>
                    <p class="feature-description">
                        Stay informed with real-time community announcements and important updates from your neighborhood.
                    </p>
                </div>

                <div class="feature-card animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="feature-icon">📅</div>
                    <h3 class="feature-title">Events Calendar</h3>
                    <p class="feature-description">
                        Never miss a community event. Browse, RSVP, and participate in local activities and gatherings.
                    </p>
                </div>

                <div class="feature-card animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="feature-icon">💬</div>
                    <h3 class="feature-title">Discussion Forum</h3>
                    <p class="feature-description">
                        Engage in meaningful conversations, share ideas, and connect with your neighbors on various topics.
                    </p>
                </div>

                <div class="feature-card animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="feature-icon">🏘️</div>
                    <h3 class="feature-title">Community Directory</h3>
                    <p class="feature-description">
                        Find and connect with community members, local services, and neighborhood resources easily.
                    </p>
                </div>

                <div class="feature-card animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="feature-icon">🔔</div>
                    <h3 class="feature-title">Smart Notifications</h3>
                    <p class="feature-description">
                        Get instant notifications about important updates, events, and discussions that matter to you.
                    </p>
                </div>

                <div class="feature-card animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="feature-icon">🛡️</div>
                    <h3 class="feature-title">Secure & Private</h3>
                    <p class="feature-description">
                        Your data is protected with enterprise-level security. Community-only access ensures privacy.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="padding: 80px 0; background: var(--bg-secondary);">
        <div class="container">
            <div class="grid grid-4">
                <div class="stats-card">
                    <div class="stats-icon">👥</div>
                    <div class="stats-content">
                        <div class="stats-label">Active Members</div>
                        <div class="stats-value">2,547</div>
                        <div class="stats-change positive">↑ 12% this month</div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-icon" style="background: var(--secondary-gradient);">📝</div>
                    <div class="stats-content">
                        <div class="stats-label">Total Posts</div>
                        <div class="stats-value">8,932</div>
                        <div class="stats-change positive">↑ 24% this month</div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-icon" style="background: var(--success-gradient);">🎉</div>
                    <div class="stats-content">
                        <div class="stats-label">Events Hosted</div>
                        <div class="stats-value">156</div>
                        <div class="stats-change positive">↑ 8% this month</div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-icon" style="background: var(--accent-gradient);">⭐</div>
                    <div class="stats-content">
                        <div class="stats-label">Satisfaction</div>
                        <div class="stats-value">98%</div>
                        <div class="stats-change positive">↑ 3% this month</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Posts Section -->
    <section style="padding: 80px 0;">
        <div class="container">
            <div class="flex-between" style="margin-bottom: 3rem;">
                <div>
                    <h2>Recent Community Posts</h2>
                    <p class="text-secondary">See what your neighbors are talking about</p>
                </div>
                <a href="{{ route('forum') }}" class="btn btn-outline">View All Posts</a>
            </div>

            <div class="grid grid-3">
                <div class="post-card">
                    <div class="post-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-meta-item">📅 2 hours ago</span>
                            <span class="post-meta-item">💬 12 comments</span>
                        </div>
                        <h3 class="post-title">Community Garden Initiative</h3>
                        <p class="post-excerpt">
                            Excited to announce our new community garden project! Join us this Saturday to help plant the
                            first seeds.
                        </p>
                        <div class="post-footer">
                            <div class="post-author">
                                <div class="post-avatar"></div>
                                <span class="post-author-name">Sarah Johnson</span>
                            </div>
                            <a href="{{ route('forum') }}" class="btn btn-ghost"
                                style="padding: 0.5rem 1rem; font-size: 0.875rem;">Read More</a>
                        </div>
                    </div>
                </div>

                <div class="post-card">
                    <div class="post-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-meta-item">📅 5 hours ago</span>
                            <span class="post-meta-item">💬 8 comments</span>
                        </div>
                        <h3 class="post-title">Neighborhood Watch Update</h3>
                        <p class="post-excerpt">
                            Monthly neighborhood watch meeting scheduled for next week. Important safety updates to discuss.
                        </p>
                        <div class="post-footer">
                            <div class="post-author">
                                <div class="post-avatar" style="background: var(--secondary-gradient);"></div>
                                <span class="post-author-name">Mike Chen</span>
                            </div>
                            <a href="{{ route('forum') }}" class="btn btn-ghost"
                                style="padding: 0.5rem 1rem; font-size: 0.875rem;">Read More</a>
                        </div>
                    </div>
                </div>

                <div class="post-card">
                    <div class="post-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-meta-item">📅 1 day ago</span>
                            <span class="post-meta-item">💬 15 comments</span>
                        </div>
                        <h3 class="post-title">Summer Block Party Planning</h3>
                        <p class="post-excerpt">
                            Let's make this year's block party the best one yet! Share your ideas and volunteer to help
                            organize.
                        </p>
                        <div class="post-footer">
                            <div class="post-author">
                                <div class="post-avatar" style="background: var(--success-gradient);"></div>
                                <span class="post-author-name">Emily Davis</span>
                            </div>
                            <a href="{{ route('forum') }}" class="btn btn-ghost"
                                style="padding: 0.5rem 1rem; font-size: 0.875rem;">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 80px 0; background: var(--bg-secondary);">
        <div class="container">
            <div class="glass-card-lg" style="text-align: center; max-width: 700px; margin: 0 auto;">
                <h2 style="margin-bottom: 1rem;">Ready to Join Our Community?</h2>
                <p class="text-secondary" style="font-size: 1.125rem; margin-bottom: 2rem;">
                    Start connecting with your neighbors today. It's free and takes less than a minute to get started.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Join Now</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
@endsection