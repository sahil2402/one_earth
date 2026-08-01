@extends('layouts.app')

@section('title', 'Domain Management | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/domain.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page">
            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif

            <section class="card page-header-card">
                <div>
                    <p class="page-tag">Settings <span>›</span> Domain</p>
                    <h1>Domains</h1>
                    <p class="intro">Manage your tour domain records, SMTP parameters, and email templates.</p>
                </div>
                @if (\App\Helpers\PermissionHelper::check($menu->slug, 'create'))
                    <a href="{{ route('menus.create', $menu) }}" class="primary-button" style="text-decoration:none;">+ Add Domain</a>
                @endif
            </section>

            <section class="card roles-table-card" style="background:#fff; border:1px solid #e6edf6; border-radius:0; padding:28px; box-shadow:0 20px 60px rgba(23,51,91,.05);">
                <div class="card-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:18px; margin-bottom:24px;">
                    <div>
                        <h2 style="font-size:22px; margin:0; color:#172f56; font-weight:800;">Domain Directory</h2>
                        <p style="margin:8px 0 0; color:#5f728f; line-height:1.7; font-size:14px;">Browse active domains, SMTP setups, and custom headers/footers.</p>
                    </div>
                    <div class="table-action-row">
                        @include('components.datatable', ['tableId' => 'domains-table'])
                    </div>
                </div>

                <div class="table-card" style="overflow:auto; margin-top:20px;">
                    @php($canUpdate = \App\Helpers\PermissionHelper::check('domain', 'update'))
                    @php($canDelete = \App\Helpers\PermissionHelper::check('domain', 'delete'))
                    <table id="domains-table" class="matrix" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f6f9fc;">
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">#</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Logo</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Domain Name</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">SMTP Host</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Email From</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Created At</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Updated At</th>
                                @if($canUpdate || $canDelete)
                                    <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($domains as $index => $d)
                                <tr>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $index + 1 }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        @if($d->logo_path)
                                            <img src="{{ custom_upload($d->logo_path) }}" alt="Logo" style="height:32px; max-width:80px; object-fit:contain; border-radius:4px; border:1px solid #e6edf6;">
                                        @else
                                            <span style="color:#a0aec0; font-size:12px;">No Logo</span>
                                        @endif
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254; font-weight:700;">{{ $d->domain_name }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;"><code>{{ $d->smtp_host ?? '—' }}</code></td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $d->email_from ?? '—' }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $d->created_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $d->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $d->updated_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $d->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    @if($canUpdate || $canDelete)
                                        <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254; white-space:nowrap;">
                                            @if($canUpdate)
                                                <a href="{{ route('menus.create', ['menu' => $menu->slug, 'edit' => $d->id]) }}" class="action-button edit-button" style="text-decoration:none; padding:6px 12px; background:#e7f2ff; color:#1761bc; border-radius:8px; font-weight:700; font-size:12px; margin-right:8px; display:inline-block;">Edit</a>
                                            @endif
                                            @if($canDelete)
                                                <form method="POST" action="{{ route('menus.domains.destroy', $d) }}" style="display:inline-block" onsubmit="return confirm('Delete this domain?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-button delete-button" style="border:0; cursor:pointer; padding:6px 12px; background:#ffebeb; color:#cf3451; border-radius:8px; font-weight:700; font-size:12px;">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty" style="padding:32px; text-align:center; color:#8a99b0; font-size:14px; border-bottom:1px solid #e7eef7;">No domains found. Click "+ Add Domain" to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ custom_asset('js/menus/domain.js') }}" defer></script>
@endpush