@extends('layouts.app')

@section('title', $menu->name.' | Travel Admin')

@push('styles')
<style>
    *{box-sizing:border-box}body{margin:0;color:#122b53;font-family:Inter,Arial,sans-serif;background:#f5f8fc}.menu-toggle{display:none}.app-shell{min-height:100vh;display:grid;grid-template-columns:258px 1fr}.app-sidebar{position:fixed;inset:0 auto 0 0;width:258px;display:flex;flex-direction:column;padding:28px 15px 20px;background:#fff;border-right:1px solid #e1e9f3}.sidebar-brand{display:flex;align-items:center;gap:10px;padding:4px 12px 28px;color:#153e85;font-size:19px;font-weight:800;text-decoration:none}.brand-symbol{color:#2181e7;font-size:24px}.sidebar-nav{padding-top:18px;border-top:1px solid #eef2f7}.nav-label{margin:0 12px 10px;color:#9baac0;font-size:10px;font-weight:800;letter-spacing:.9px;text-transform:uppercase}.nav-item{height:43px;display:flex;align-items:center;gap:13px;margin:3px 0;padding:0 13px;border-radius:9px;color:#6d7e9c;font-size:13px;font-weight:700;text-decoration:none}.nav-item svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8}.settings-menu{margin:3px 0}.settings-menu summary{list-style:none;cursor:pointer}.settings-menu summary::-webkit-details-marker{display:none}.settings-menu .nav-item b{margin-left:auto;font-size:15px}.settings-menu[open] .nav-item b{transform:rotate(180deg)}.submenu-item{display:flex;align-items:center;gap:10px;height:36px;margin:2px 0 2px 22px;padding:0 12px;border-radius:8px;color:#7d8da7;font-size:12px;font-weight:600;text-decoration:none}.submenu-item.is-current{color:#1760c2;background:#f1f7ff}.submenu-item span{width:5px;height:5px;border-radius:50%;background:#b3c0d2}.submenu-item.is-current span{background:#1f70d2}.sidebar-footer{margin-top:auto;padding:17px 13px 6px;border-top:1px solid #eef2f7}.footer-badge{display:inline-block;padding:4px 8px;border-radius:20px;color:#1761bc;background:#e7f2ff;font-size:9px;font-weight:800}.sidebar-footer p{margin:10px 0 3px;font-size:12px;font-weight:700}.sidebar-footer small{color:#8796ad;font-size:10px}.app-main{min-width:0;grid-column:2}.app-navbar{min-height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 38px;background:#fff;border-bottom:1px solid #e1e9f3}.navbar-title span{display:block;font-size:15px;font-weight:800}.navbar-title small{display:block;margin-top:3px;color:#8a99b0;font-size:11px}.navbar-actions{display:flex;align-items:center;gap:22px}.account-summary{display:flex;align-items:center;gap:9px}.account-summary>span:last-child{display:flex;flex-direction:column}.account-summary strong{font-size:11px}.account-summary small{color:#8a99b0;font-size:10px}.account-avatar{width:30px;height:30px;display:grid;place-items:center;border-radius:50%;color:#fff;background:linear-gradient(135deg,#2278dc,#153f8e);font-size:11px;font-weight:800}.logout-button{height:35px;padding:0 13px;border:1px solid #d5e1f0;border-radius:7px;color:#175db9;background:#fff;font:700 12px Inter,Arial,sans-serif;cursor:pointer}.page-content{max-width:980px;margin:0 auto;padding:38px clamp(28px,4vw,58px)}.breadcrumb{color:#7789a7;font-size:12px}.breadcrumb b{color:#1b63bf}.page-heading{margin:13px 0 5px;font-size:30px;letter-spacing:-1.2px}.page-intro{margin:0;color:#7184a4;font-size:14px}.blank-card{min-height:260px;margin-top:27px;border:1px dashed #d5e0ee;border-radius:13px;background:rgba(255,255,255,.55)}@media(max-width:900px){.app-shell{display:block}.app-sidebar{transform:translateX(-102%)}.app-main{min-width:0}.account-summary{display:none}.app-navbar{padding:0 22px}}@media(max-width:570px){.app-navbar{padding:0 15px}.page-content{padding:30px 18px}.page-heading{font-size:27px}}
</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">{{ $menu->parent?->name ?? 'Workspace' }} <span>›</span> <b>{{ $menu->name }}</b></div>
            <h1 class="page-heading">{{ $menu->name }}</h1>
            <p class="page-intro">This workspace page is ready for its module content.</p>
            <section class="blank-card" aria-label="{{ $menu->name }} workspace"></section>
        </main>
    </div>
</div>
@endsection
