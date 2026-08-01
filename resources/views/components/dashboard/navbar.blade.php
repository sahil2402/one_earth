<header class="app-navbar">
    <button class="menu-toggle" type="button" aria-label="Toggle navigation" onclick="document.body.classList.toggle('sidebar-open')">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
    <div class="navbar-title"><span>{{ $navbarTitle ?? 'Overview' }}</span><small>Travel operations workspace</small></div>
    <div class="navbar-actions">
        @if(isset($role) && strtolower($role) === 'owner')
            <div class="owner-menu" style="margin-right:12px;position:relative">
                <button type="button" class="owner-btn" aria-haspopup="true" aria-expanded="false">
                    <span class="owner-label"><strong>{{ ucfirst($role) }}</strong><small>{{ $email }}</small></span>
                    <svg class="chev" viewBox="0 0 24 24" width="14" height="14"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="owner-dropdown" role="menu" aria-hidden="true">
                    <a class="owner-item" href="{{ route('settings.website') }}">Website Settings</a>
                </div>
            </div>
        @else
            <div class="account-summary"><span><strong>{{ ucfirst($role) }}</strong><small>{{ $email }}</small></span></div>
        @endif
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" type="submit">Sign out</button></form>
    </div>
</header>
