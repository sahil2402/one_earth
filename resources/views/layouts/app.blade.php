<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Travel Admin')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
     <style>
        body {
            zoom: {{ $websiteSetting->zoom_level ?? '100%' }};
        }
    </style>
    <style>
        .owner-menu { position: relative; }
        .owner-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; border: 1px solid #e6eef7; background: #fff; cursor: pointer; }
        .owner-btn .owner-label { display: flex; flex-direction: column; align-items: flex-start; }
        .owner-btn .owner-label strong { font-size: 12px; }
        .owner-btn .owner-label small { font-size: 11px; color: #8a99b0; }
        .owner-dropdown { position: absolute; right: 0; top: calc(100% + 8px); background: #fff; border: 1px solid #e6eef7; border-radius: 8px; box-shadow: 0 10px 30px rgba(23, 43, 77, .08); padding: 8px; min-width: 180px; display: none; z-index: 1000; }
        .owner-item { display: block; padding: 8px 12px; color: #174f9b; text-decoration: none; border-radius: 6px; }
        .owner-item:hover { background: #f1f7ff; }
        .owner-menu.open .owner-dropdown { display: block; }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    <script src="{{ custom_asset('js/roles.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
