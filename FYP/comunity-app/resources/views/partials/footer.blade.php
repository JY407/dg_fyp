<footer class="footer" style="position: relative; background-color: #0f172a; overflow: hidden; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05);">
    {{-- Background image with overlay --}}
    <div style="position: absolute; inset: 0; background-image: url('{{ asset('images/multicultural-hero.png') }}'); background-size: cover; background-position: center bottom; filter: brightness(0.3) saturate(1.2); z-index: 0; opacity: 0.8;"></div>
    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,1) 0%, rgba(15,23,42,0.8) 40%, rgba(15,23,42,0.6) 100%); z-index: 1;"></div>
    
    <div class="footer-container" style="position: relative; z-index: 10;">
        <div class="footer-grid">
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="footer-logo">LC</div>
                    <span class="footer-brand-name">Lcare</span>
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
                <h4 class="footer-title">Our Heritage</h4>
                <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 12px; line-height: 1.5;">
                    Celebrating the rich tapestry of cultures that make our community unique.
                </p>
                <div style="display:flex; gap:8px; font-size:18px; margin-bottom:12px;">
                    <span title="Islamic/Malay">🕌</span>
                    <span title="Chinese/Buddhist">🏮</span>
                    <span title="Hindu/Indian">🪔</span>
                </div>
                <div style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                    <span class="mc-pill mc-pill-malay" style="font-size: 9px; padding: 2px 6px; background: rgba(5,150,105,0.1); border: 1px solid rgba(5,150,105,0.2);">🌙 Malay</span>
                    <span class="mc-pill mc-pill-chinese" style="font-size: 9px; padding: 2px 6px; background: rgba(220,38,38,0.1); border: 1px solid rgba(220,38,38,0.2);">🏮 Chinese</span>
                    <span class="mc-pill mc-pill-indian" style="font-size: 9px; padding: 2px 6px; background: rgba(124,58,237,0.1); border: 1px solid rgba(124,58,237,0.2);">🪔 Indian</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                © {{ date('Y') }} Lcare. All rights reserved.
            </p>
            <ul class="footer-bottom-links">
                <li><a href="#" class="footer-bottom-link">Privacy</a></li>
                <li><a href="#" class="footer-bottom-link">Terms</a></li>
                <li><a href="#" class="footer-bottom-link">Cookies</a></li>
            </ul>
        </div>
    </div>
</footer>