<style>
    #sidebar-menu-search:focus {
        border-color: #1761bc !important;
        box-shadow: 0 0 0 3px rgba(23, 97, 188, 0.1);
    }
</style>
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
    
    {{-- Search Filter Input --}}
    <div style="padding: 10px 12px 14px; border-bottom: 1px solid #eef2f7;">
        <input id="sidebar-menu-search" type="text" placeholder="Search menu..." style="width: 100%; height: 38px; padding: 0 12px 0 35px; border: 1.5px solid #e1e9f3; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e2d4a; background: #fff url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%237d8da7%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Ccircle cx=%2211%22 cy=%2211%22 r=%228%22/%3E%3Cpath d=%22m21 21-4.3-4.3%22/%3E%3C/svg%3E') no-repeat left 10px center; background-size: 15px; outline: none; transition: all 0.2s ease;">
    </div>

    <nav class="sidebar-nav" aria-label="Primary navigation" style="padding-top: 12px; border-top: none;">
        <p class="nav-label">Workspace</p>
        <a class="nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span>Dashboard</span></a>
        
        @foreach ($navigationMenus as $menu)
            @php
                $isSettings = $menu->slug === 'settings';
                $isChildActive = false;
                
                // If it is the settings parent menu, check if Menu Create is active
                if ($isSettings && (request()->routeIs('menu.create') || request()->routeIs('menu.*'))) {
                    $isChildActive = true;
                }
                
                // Check if any of the child submenus are active
                foreach ($menu->children as $child) {
                    $currentMenuRoute = request()->route('menu');
                    if ($currentMenuRoute && $currentMenuRoute->slug === $child->slug) {
                        $isChildActive = true;
                    }
                }
            @endphp
            
            <details class="settings-menu" {{ $isChildActive ? 'open' : '' }}>
                <summary class="nav-item {{ $isChildActive ? 'is-active' : '' }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="m19.4 15 .1.1 1.3 1-1.8 3.1-1.5-.6-1.3.8-.2 1.6H13v-1.6l-1.3-.8-1.5.6-1.8-3.1 1.3-1 .1-.1v-1.7l-.1-.1-1.3-1 1.8-3.1 1.5.6L13 8.6V7h3.6v1.6l1.3.8 1.5-.6 1.8 3.1-1.3 1-.1.1V15Z"/></svg><span>{{ $menu->name }}</span><b>⌄</b></summary>
                
                @if ($isSettings && \App\Helpers\PermissionHelper::check('menu-create', 'view'))
                    <a class="submenu-item {{ (request()->routeIs('menu.create') || request()->routeIs('menu.*')) ? 'is-current' : '' }}" href="{{ route('menu.create') }}"><span></span>Menu Create</a>
                @endif
                
                @foreach ($menu->children as $child)
                    @if ($child->slug !== 'menu-create')
                        @php
                            $isCurrentSub = false;
                            $currentMenuRoute = request()->route('menu');
                            if ($currentMenuRoute && $currentMenuRoute->slug === $child->slug) {
                                $isCurrentSub = true;
                            }
                        @endphp
                        <a class="submenu-item {{ $isCurrentSub ? 'is-current' : '' }}" href="{{ route('menus.show', $child) }}"><span></span>{{ $child->name }}</a>
                    @endif
                @endforeach
            </details>
        @endforeach
    </nav>
    <div class="sidebar-footer"><span class="footer-badge">{{ strtoupper($role) }}</span><p>Owner access</p><small>Role management will be available here.</small></div>
</aside>
