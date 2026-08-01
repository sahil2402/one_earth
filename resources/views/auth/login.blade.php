@extends('layouts.app')

@php $websiteSetting = \App\Models\WebsiteSetting::first(); @endphp
@section('title', 'Admin Login | ' . ($websiteSetting->site_name ?? 'Travel Admin'))

@push('styles')
<style>
    :root { color-scheme: light; --navy:#102b60; --blue:#1754b8; --soft-blue:#edf4fc; --line:#d9e2ee; --muted:#6e7e9e; }
    * { box-sizing: border-box; zoom: 1 !important; }
    body { margin:0; min-width:320px; color:var(--navy); font-family:Inter,Arial,sans-serif; background:#f7faff; }
    .login-shell { min-height:100vh; display:grid; grid-template-columns:minmax(400px,39.1%) 1fr; overflow:hidden; }
    .travel-side { position:relative; isolation:isolate; min-height:100vh; padding:clamp(42px,6.2vh,72px) clamp(46px,5.9vw,86px) 48px; display:flex; flex-direction:column; justify-content:space-between; color:#fff; background-image:linear-gradient(180deg,rgba(11,57,126,.88) 0%,rgba(14,60,132,.69) 49%,rgba(5,39,96,.88) 100%),url('{{ asset('images/travel-login-scene.png') }}'); background-size:100% 100%, 260% auto; background-position:left top,left center; background-repeat:no-repeat; }
    .travel-side:before { content:''; z-index:-1; position:absolute; inset:0; background:radial-gradient(circle at 77% 55%,transparent 0 8%,rgba(11,52,118,.17) 55%,rgba(7,37,87,.30) 100%); }
    .brand { display:flex; align-items:center; gap:13px; font-size:20px; font-weight:800; letter-spacing:-.55px; }.brand-icon { width:50px; height:50px; display:grid; place-items:center; border:2px solid rgba(255,255,255,.98); border-radius:50%; }.brand-icon svg { width:28px; height:28px; fill:#fff; transform:rotate(-10deg); }
    .travel-copy { max-width:535px; margin-bottom:clamp(18px,4.2vh,55px); }.travel-copy h1 { max-width:535px; margin:0; font-size:clamp(39px,3.3vw,53px); line-height:1.06; letter-spacing:-2.4px; font-weight:800; }.travel-copy p { max-width:445px; margin:19px 0 0; font-size:16px; line-height:1.65; color:rgba(255,255,255,.94); }.flight-line { width:min(555px,100%); margin-top:42px; border-top:2px dashed rgba(255,255,255,.8); transform:rotate(-4deg); transform-origin:left center; }
    .login-side { position:relative; display:grid; place-items:center; padding:42px; overflow:hidden; background:linear-gradient(135deg,#f9fbff 0%,#f6f9fd 52%,#eff5fb 100%); }.login-side:before { content:''; position:absolute; inset:0; opacity:.56; background-image:radial-gradient(#c5d3e8 1.45px,transparent 1.45px); background-size:9px 9px; background-position:calc(100% + 110px) 54px; background-repeat:no-repeat; }.login-side:after { content:''; position:absolute; width:520px; height:520px; right:-312px; bottom:-280px; border:1px dashed #cad7eb; border-radius:50%; box-shadow:0 0 0 70px rgba(250,252,255,.56); }
    .login-card { width:min(100%,548px); position:relative; z-index:1; padding:42px 54px 30px; border:1px solid rgba(233,238,246,.95); border-radius:18px; background:rgba(255,255,255,.96); box-shadow:0 26px 54px rgba(29,64,117,.14); }.card-head { text-align:center; }.logo-art { width:84px; height:68px; margin:0 auto 9px; }.card-head h2 { margin:0; font-size:28px; line-height:1.2; letter-spacing:-1.15px; }.card-head p { margin:4px 0 0; color:#66769a; font-size:16px; }.ornament { display:flex; align-items:center; gap:12px; margin:24px 0 22px; color:#9badca; }.ornament:before,.ornament:after { content:''; height:1px; flex:1; background:#dbe2ed; }.ornament svg { width:23px; height:23px; fill:#a7b4ca; transform:rotate(-13deg); }
    .field { margin-bottom:17px; }.field label { display:block; margin-bottom:7px; font-size:15px; font-weight:700; }.field-box { height:54px; display:flex; align-items:center; gap:13px; padding:0 15px; border:1px solid #ccd7e6; border-radius:10px; background:#fff; transition:.2s ease; }.field-box:focus-within { border-color:#1e69c9; box-shadow:0 0 0 3px rgba(31,102,201,.11); }.field-box > svg { flex:0 0 22px; width:22px; height:22px; fill:none; stroke:#7384a4; stroke-width:1.8; }.field-box input { min-width:0; width:100%; border:0; outline:0; color:#273c66; background:transparent; font:inherit; font-size:15px; }.field-box input::placeholder { color:#7787a5; }.password-toggle { border:0; padding:4px; display:grid; place-items:center; color:#7082a4; background:transparent; cursor:pointer; }.password-toggle svg { width:21px; height:21px; fill:none; stroke:currentColor; stroke-width:2; }.error { margin:7px 0 0; color:#cf3451; font-size:12px; }.options { display:flex; align-items:center; justify-content:space-between; gap:14px; margin:1px 0 23px; font-size:14px; }.remember { display:inline-flex; align-items:center; gap:10px; font-weight:600; white-space:nowrap; cursor:pointer; }.remember input { width:20px; height:20px; margin:0; accent-color:#1f61bf; }.help-link { color:#175dc1; font-weight:700; text-decoration:none; }.submit { width:100%; height:55px; border:0; border-radius:10px; color:#fff; font:700 17px Inter,Arial,sans-serif; background:linear-gradient(100deg,#1249aa,#1868cc); box-shadow:0 14px 25px rgba(19,81,180,.26); cursor:pointer; transition:transform .18s,box-shadow .18s; }.submit:hover { transform:translateY(-1px); box-shadow:0 17px 29px rgba(19,81,180,.33); }.button-lock { width:20px; height:20px; margin-right:9px; vertical-align:-4px; fill:none; stroke:currentColor; stroke-width:2; }.secure { margin:29px 0 0; color:#697a9c; text-align:center; font-size:12px; line-height:1.45; }.secure strong { display:block; margin-bottom:5px; font-size:13px; }.shield { width:17px; height:17px; margin-right:6px; vertical-align:-4px; fill:#7d8eac; }
    @media (max-width:1050px) { .login-shell{grid-template-columns:minmax(350px,36%) 1fr}.travel-side{padding-left:42px;padding-right:42px}.travel-copy h1{font-size:48px}.login-card{padding-left:44px;padding-right:44px} }
    @media (max-width:790px) { .login-shell{display:block}.travel-side{display:none}.login-side{min-height:100vh;padding:28px 18px}.login-card{min-height:auto;padding:43px 32px 35px}.secure{margin-top:35px} }
    @media (max-width:420px) { .login-card{padding:35px 21px 28px}.card-head h2{font-size:27px}.card-head p{font-size:15px}.options{margin-bottom:28px}.field-box{gap:11px;padding:0 13px}.logo-art{width:88px;height:72px}.login-side{padding:14px}.secure{font-size:12px} }
</style>
@endpush

@section('content')
<main class="login-shell">
    <aside class="travel-side" aria-label="{{ $websiteSetting->site_name ?? 'Travel Admin' }}">
        <div class="brand">
            <span class="brand-icon">
                @if(!empty($websiteSetting->logo_path))
                    <img src="/{{ $websiteSetting->logo_path }}" alt="{{ $websiteSetting->site_name ?? 'Logo' }}" style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.98)">
                @else
                    <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M29.7 16.2c0-1-.8-1.8-1.8-1.9l-9.4-1.1-4.8-7.8a1.8 1.8 0 0 0-3.2.4l-1.9 6.6-5.1-.6-2.2-3.1-1.6.2 1 3.6-1.3 3.1 1.6.2 2.8-2.4 5.1.6.1 6.8c0 .8.5 1.5 1.3 1.7.8.2 1.6-.2 1.9-.9l3.1-6.6 11.3 1.3h.2c1 0 1.8-.8 1.8-1.8Z"/></svg>
                @endif
            </span>
            <span style="font-weight:800">{{ $websiteSetting->site_name ?? 'Travel Admin' }}</span>
        </div>
        <div class="travel-copy"><h1>Every great journey starts with a plan.</h1><p>Manage your travel business, bookings, and experiences from one secure place.</p><div class="flight-line"></div></div>
    </aside>
    <section class="login-side">
        <form class="login-card" method="POST" action="{{ route('login.store') }}">
            @csrf
            <header class="card-head">
                @if(!empty($websiteSetting->logo_path))
                    <img class="logo-art" src="/{{ $websiteSetting->logo_path }}" alt="{{ $websiteSetting->site_name ?? 'Logo' }}">
                @else
                    <svg class="logo-art" viewBox="0 0 120 95" aria-hidden="true"><defs><linearGradient id="sky" x1="18" y1="5" x2="87" y2="76"><stop stop-color="#22a0ed"/><stop offset="1" stop-color="#164eb8"/></linearGradient></defs><path d="M21 59A38 38 0 1 1 87 48" fill="none" stroke="url(#sky)" stroke-width="6" stroke-linecap="round"/><path d="M24 61c14 16 36 24 59 17" fill="none" stroke="#1852b7" stroke-width="6" stroke-linecap="round"/><path d="M43 75c10-8 24-9 33 1-10 7-23 9-35 4Z" fill="#ffae1a"/><path d="M99 35 81 40 68 34l-10 4 9 5-16 10-10-2-4 3 14 5 15-8 10 3 4-3-6-5 18-4 9-5-2-3Z" fill="#1553ba"/></svg>
                @endif
                <h2>{{ $websiteSetting->site_name ?? 'Travel Admin' }}</h2><p>Admin Login</p>
            </header>
            <div class="ornament"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M30 15.2 19 14l-5.5-9a2.1 2.1 0 0 0-3.8.5l-2.1 7.6-5.9-.7-2.6-3.7-1.8.3 1.1 4.2-1.5 3.7 1.8.3 3.2-2.9 5.9.7.1 7.9c0 1 .6 1.8 1.6 2 1 .2 1.9-.3 2.3-1.2l3.6-7.7L28 17h.2c1 0 1.8-.8 1.8-1.8Z"/></svg></div>
            <div class="field"><label for="email">Email Address</label><div class="field-box"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" autocomplete="email" required autofocus></div>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="field"><label for="password">Password</label><div class="field-box"><svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-label="Show password" onclick="const password=document.getElementById('password'),icon=this.querySelector('svg');password.type=password.type==='password'?'text':'password';icon.style.opacity=password.type==='password'?'1':'.45';"><svg viewBox="0 0 24 24"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.8"/></svg></button></div>@error('password')<p class="error">{{ $message }}</p>@enderror</div>
            <button class="submit" type="submit"><svg class="button-lock" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Login</button>
            <footer class="secure"><strong><svg class="shield" viewBox="0 0 24 24"><path d="M12 2 20 5v6c0 5.2-3.4 9.8-8 11-4.6-1.2-8-5.8-8-11V5l8-3Zm-1 5v10l6-3.2V7.2L11 7Z"/></svg>Secure Admin Login</strong>© {{ date('Y') }} {{ $websiteSetting->site_name ?? 'Travel Admin' }}. All rights reserved.</footer>
        </form>
    </section>
</main>
@endsection
