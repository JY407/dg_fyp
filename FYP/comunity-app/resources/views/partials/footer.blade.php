<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="footer-logo">CC</div>
                    <span class="footer-brand-name">Community Connect</span>
                </div>
                <p class="footer-description">
                    Building stronger communities through better communication and engagement.
                </p>
                <div class="footer-social">
                    <a href="#" class="footer-social-link">📘</a>
                    <a href="#" class="footer-social-link">🐦</a>
                    <a href="#" class="footer-social-link">📷</a>
                    <a href="#" class="footer-social-link">💼</a>
                </div>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}" class="footer-link">Home</a></li>
                    <li><a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a></li>
                    <li><a href="{{ route('announcements') }}" class="footer-link">Announcements</a></li>
                    <li><a href="{{ route('events') }}" class="footer-link">Events</a></li>
                    <li><a href="{{ route('forum') }}" class="footer-link">Forum</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Resources</h4>
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Help Center</a></li>
                    <li><a href="#" class="footer-link">Community Guidelines</a></li>
                    <li><a href="#" class="footer-link">Privacy Policy</a></li>
                    <li><a href="#" class="footer-link">Terms of Service</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Newsletter</h4>
                <p style="color: var(--text-muted); margin-bottom: 1rem;">
                    Subscribe to get weekly community updates
                </p>
                <form class="footer-newsletter">
                    <input type="email" class="footer-newsletter-input" placeholder="Your email">
                    <button type="submit" class="footer-newsletter-btn">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                © {{ date('Y') }} Community Connect. All rights reserved.
            </p>
            <ul class="footer-bottom-links">
                <li><a href="#" class="footer-bottom-link">Privacy</a></li>
                <li><a href="#" class="footer-bottom-link">Terms</a></li>
                <li><a href="#" class="footer-bottom-link">Cookies</a></li>
            </ul>
        </div>
    </div>
</footer>