<aside class="app-sidebar">
    <a class="sidebar-brand" href="{{ route('dashboard') }}" style="display:flex;flex-direction:column;gap:6px">
        <div class="brand-symbol" style="display:block">
            @if(!empty($websiteSetting?->logo_path))
                <img src="{{ custom_upload($websiteSetting->logo_path) }}" alt="logo" style="height:40px;border-radius:6px;object-fit:contain">
            @else
                <span style="font-size:24px;line-height:1">✈</span>
            @endif
        </div>
        <div style="font-weight:800;color:#153e85;font-size:18px;line-height:1.05">{{ $websiteSetting->site_name ?? 'Travel Admin' }}</div>
    </a>
    <nav class="sidebar-nav" aria-label="Primary navigation">
        <p class="nav-label">Workspace</p>
        <a class="nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span>Dashboard</span></a>
        @foreach ($navigationMenus as $menu)
            @php($isSettings = $menu->slug === 'settings')
            @php($isMenuArea = $isSettings && request()->routeIs('menu.*'))
            <details class="settings-menu" {{ ($isSettings || $menu->children->isNotEmpty()) ? 'open' : '' }}>
                <summary class="nav-item {{ $isMenuArea ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="m19.4 15 .1.1 1.3 1-1.8 3.1-1.5-.6-1.3.8-.2 1.6H13v-1.6l-1.3-.8-1.5.6-1.8-3.1 1.3-1 .1-.1v-1.7l-.1-.1-1.3-1 1.8-3.1 1.5.6L13 8.6V7h3.6v1.6l1.3.8 1.5-.6 1.8 3.1-1.3 1-.1.1V15Z"/></svg><span>{{ $menu->name }}</span><b>⌄</b></summary>
                @if ($isSettings && \App\Helpers\PermissionHelper::check('menu-create', 'view'))<a class="submenu-item {{ request()->routeIs('menu.create') ? 'is-current' : '' }}" href="{{ route('menu.create') }}"><span></span>Menu Create</a>@endif
                @foreach ($menu->children as $child)
                    @if ($child->slug !== 'menu-create')
                        <a class="submenu-item {{ request()->routeIs('menus.show') && request()->route('menu')?->is($child) ? 'is-current' : '' }}" href="{{ route('menus.show', $child) }}"><span></span>{{ $child->name }}</a>
                    @endif
                @endforeach
            </details>
        @endforeach
    </nav>
    <div class="sidebar-footer"><span class="footer-badge">{{ strtoupper($role) }}</span><p>Owner access</p><small>Role management will be available here.</small></div>
</aside>
