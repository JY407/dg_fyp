<div>
    <!-- Hero Section -->
    <section class="hero" style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <div class="hero-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h1 class="animate-fade-in" style="margin-bottom: 1.5rem;">
                    Connect, Communicate, Community
                </h1>
                <p class="animate-fade-in" style="font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 2rem; animation-delay: 0.2s;">
                    Your central hub for community engagement, announcements, events, and meaningful conversations. Stay connected with your neighbors and build a stronger community together.
                </p>
                <div class="hero-buttons" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Get Started</a>
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
                <!-- Feature cards here -->
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="padding: 80px 0; background: var(--bg-secondary);">
        <div class="container">
            <div class="grid grid-4">
                <!-- Stats cards here -->
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
                <a href="{{ url('/forum') }}" class="btn btn-outline">View All Posts</a>
            </div>

            <div class="grid grid-3">
                <!-- Post cards here -->
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
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Join Now</a>
                    <a href="{{ url('/contact') }}" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
</div>
