<nav class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <div class="navbar-logo">CC</div>
            <span>Community Connect</span>
        </a>

        <button class="navbar-toggle" id="navbarToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="navbar-menu" id="navbarMenu">
            <li class="navbar-item"><a href="{{ url('/') }}" class="navbar-link active">Home</a></li>
            <li class="navbar-item"><a href="{{ url('/dashboard') }}" class="navbar-link">Dashboard</a></li>
            <li class="navbar-item"><a href="{{ url('/announcements') }}" class="navbar-link">Announcements</a></li>
            <li class="navbar-item"><a href="{{ url('/events') }}" class="navbar-link">Events</a></li>
            <li class="navbar-item"><a href="{{ url('/forum') }}" class="navbar-link">Forum</a></li>
            <li class="navbar-item"><a href="{{ url('/contact') }}" class="navbar-link">Contact</a></li>
            <li class="navbar-item">
                <div class="navbar-user">
                    <div class="navbar-avatar">JD</div>
                    <span class="navbar-user-name">John Doe</span>
                </div>
            </li>
        </ul>
    </div>
</nav>
