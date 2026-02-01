@extends('layouts.app')

@section('title', 'Contact Us - Community Connect')

@section('content')
    <section style="padding: 120px 0 80px; margin-top: 70px;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto;">
                <h1 style="text-align: center;">Get In Touch</h1>
                <p class="text-secondary" style="text-align: center; margin-bottom: 3rem;">
                    Have questions or suggestions? We'd love to hear from you!
                </p>

                <div class="glass-card-lg">
                    <form>
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-input" placeholder="Your name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" placeholder="your@email.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-input" placeholder="What is this about?">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea class="form-textarea" placeholder="Your message here..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                </div>

                <div class="grid grid-3" style="margin-top: 3rem; text-align: center;">
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📧</div>
                        <div class="text-muted" style="font-size: 0.875rem;">Email</div>
                        <div class="text-primary">hello@community.com</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📞</div>
                        <div class="text-muted" style="font-size: 0.875rem;">Phone</div>
                        <div class="text-primary">+1 (555) 123-4567</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📍</div>
                        <div class="text-muted" style="font-size: 0.875rem;">Address</div>
                        <div class="text-primary">123 Community St</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection