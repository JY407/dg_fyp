<nav class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="{{ route('home') }}" class="navbar-brand">
            <div class="navbar-logo">CC</div>
            <span>Community Connect</span>
        </a>

        <div class="navbar-actions" style="display: flex; align-items: center; gap: 1rem;">
            @auth
                <!-- Notification Bell with Dropdown -->
                <div class="navbar-item" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="navbar-link"
                        style="display: flex; align-items: center; justify-content: center; background: transparent; border: none; cursor: pointer; position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                        </svg>
                        <!-- Badge for new notifications -->
                        <span class="notification-badge">3</span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95" class="glass-card notification-dropdown"
                        style="position: absolute; top: 100%; right: 0; width: 320px; z-index: 50; margin-top: 0.5rem; display: none; padding: 0;">

                        <div class="notification-header"
                            style="padding: 1rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; font-size: 1rem;">Notifications</h4>
                            <span class="text-muted" style="font-size: 0.75rem;">3 Unread</span>
                        </div>

                        <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                            <!-- Mocked Notifications -->
                            <a href="{{ route('announcements') }}" class="notification-item"
                                style="padding: 1rem; display: flex; gap: 1rem; text-decoration: none; transition: background 0.2s;">
                                <div class="notification-icon"
                                    style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </div>
                                <div class="notification-content">
                                    <p
                                        style="margin: 0; font-size: 0.875rem; color: var(--text-primary); font-weight: 500;">
                                        New Community Meeting Scheduled</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">2 hours ago</p>
                                </div>
                            </a>

                            <a href="{{ route('events') }}" class="notification-item"
                                style="padding: 1rem; display: flex; gap: 1rem; text-decoration: none; transition: background 0.2s;">
                                <div class="notification-icon"
                                    style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: var(--secondary-gradient); display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                                <div class="notification-content">
                                    <p
                                        style="margin: 0; font-size: 0.875rem; color: var(--text-primary); font-weight: 500;">
                                        Upcoming: Weekend Cleanup Event</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">1 day ago</p>
                                </div>
                            </a>

                            <a href="{{ route('forum') }}" class="notification-item"
                                style="padding: 1rem; display: flex; gap: 1rem; text-decoration: none; transition: background 0.2s;">
                                <div class="notification-icon"
                                    style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="notification-content">
                                    <p
                                        style="margin: 0; font-size: 0.875rem; color: var(--text-primary); font-weight: 500;">
                                        New reply in 'Security Concerns'</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">3 days ago</p>
                                </div>
                            </a>
                        </div>

                        <div class="notification-footer"
                            style="padding: 0.75rem; border-top: 1px solid var(--glass-border); text-align: center;">
                            <a href="{{ route('announcements') }}"
                                style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600;">View All
                                Activity</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="navbar-item" style="position: relative;" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="navbar-link btn-ghost"
                        style="display: flex; align-items: center; gap: 0.5rem; background: transparent; border: none; cursor: pointer; color: inherit; font-family: inherit; font-size: inherit; padding: 0.5rem 1rem;">
                        <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            :style="{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s' }">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <!-- Profile Menu -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95" class="glass-card"
                        style="position: absolute; top: 100%; right: 0; width: 200px; padding: 0.5rem; display: flex; flex-direction: column; gap: 0.25rem; z-index: 50; margin-top: 0.5rem; display: none;">

                        <a href="{{ route('profile.edit') }}" class="btn btn-ghost"
                            style="justify-content: flex-start; border-radius: 8px; font-size: 0.9rem;">
                            Profile Page
                        </a>

                        <a href="{{ route('profile.edit') }}" class="btn btn-ghost"
                            style="justify-content: flex-start; border-radius: 8px; font-size: 0.9rem;">
                            Settings
                        </a>

                        <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 0.25rem 0;"></div>

                        <form method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" class="btn btn-ghost"
                                style="width: 100%; justify-content: flex-start; border-radius: 8px; font-size: 0.9rem; color: #f5576c;">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('login') }}" class="btn btn-ghost" style="padding: 0.5rem 1rem;">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>