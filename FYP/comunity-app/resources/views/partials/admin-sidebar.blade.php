<aside class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="sidebar-logo" style="background: #ef4444;">Ad</div>
            <span>{{ __('app.app_name_admin') }}</span>
        </a>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>{{ __('app.nav_admin_dashboard') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.visitors.create') }}"
                class="sidebar-link {{ request()->routeIs('admin.visitors.create') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>{{ __('app.nav_record_visitor') }}</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.verifications') }}"
                class="sidebar-link {{ request()->routeIs('admin.verifications') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>{{ __('app.nav_verifications') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.security-duties') }}"
                class="sidebar-link {{ request()->routeIs('admin.security-duties') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>{{ __('app.nav_security_roster') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.services-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.services-management') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <span>{{ __('app.nav_services') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.culture-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.culture-management') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 0-10-10zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                <span>{{ __('app.nav_culture') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.events-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.events-management') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path>
                    <path d="M8 18h.01"></path><path d="M12 18h.01"></path>
                </svg>
                <span>{{ __('app.nav_events') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.contact-messages') }}"
                class="sidebar-link {{ request()->routeIs('admin.contact-messages') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span>{{ __('app.nav_messages') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.facilities') }}"
                class="sidebar-link {{ request()->routeIs('admin.facilities') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/><rect x="7" y="9" width="2" height="2"/><rect x="15" y="9" width="2" height="2"/><rect x="7" y="13" width="2" height="2"/><rect x="15" y="13" width="2" height="2"/>
                </svg>
                <span>{{ __('app.nav_facilities') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.announcements-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.announcements-management') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span>{{ __('app.nav_announcements') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.forum-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.forum-management') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                    </path>
                </svg>
                <span>{{ __('app.nav_forum') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.emergencies-management') }}"
                class="sidebar-link {{ request()->routeIs('admin.emergencies-management') ? 'active' : '' }}" style="color: #ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                    </path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>{{ __('app.nav_emergencies') }}</span>
            </a>
        </li>
    </ul>

    {{-- Language Switcher --}}
    <div style="padding: 0.75rem 1rem; border-top: 1px solid rgba(255,255,255,0.07);">
        <p style="font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;">
            {{ __('app.language') }}
        </p>
        <div style="display: flex; gap: 0.4rem;">
            <a href="{{ route('lang.switch', 'en') }}"
               style="flex: 1; text-align: center; padding: 0.3rem 0; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;
                      {{ app()->getLocale() === 'en' ? 'background: #ef4444; color: #fff;' : 'background: rgba(255,255,255,0.06); color: #94a3b8;' }}">
                EN
            </a>
            <a href="{{ route('lang.switch', 'ms') }}"
               style="flex: 1; text-align: center; padding: 0.3rem 0; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;
                      {{ app()->getLocale() === 'ms' ? 'background: #ef4444; color: #fff;' : 'background: rgba(255,255,255,0.06); color: #94a3b8;' }}">
                MY
            </a>
            <a href="{{ route('lang.switch', 'zh') }}"
               style="flex: 1; text-align: center; padding: 0.3rem 0; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none;
                      {{ app()->getLocale() === 'zh' ? 'background: #ef4444; color: #fff;' : 'background: rgba(255,255,255,0.06); color: #94a3b8;' }}">
                中
            </a>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar" style="background: #ef4444;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">{{ __('app.administrator') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>{{ __('app.nav_logout') }}</span>
            </button>
        </form>
    </div>
</aside>