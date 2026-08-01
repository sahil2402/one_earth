@extends('layouts.app')

@section('title', 'Users | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/users.css') }}">
<!-- Toastr for alerts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <section class="card page-header-card" style="display:flex; align-items:flex-start; justify-content:space-between; gap:24px; margin-bottom:24px; min-height:auto; padding:28px;">
                <div>
                    <div class="breadcrumb" style="margin:0 0 10px;">Settings <span>›</span> <b>Users</b></div>
                    <h1 class="page-heading" style="margin:0 0 6px; font-size:34px;">Users</h1>
                    <p class="page-intro" style="margin:0; color:#7184a4; font-size:14px;">Manage administrative workspace users, roles, and designations.</p>
                </div>
                @if (\App\Helpers\PermissionHelper::check($menu->slug, 'create'))
                    <a href="{{ route('menus.create', $menu) }}" class="primary-button">+ Add User</a>
                @endif
            </section>

            <section class="card roles-table-card" style="background:#fff; border:1px solid #e6edf6; border-radius:24px; padding:28px; box-shadow:0 20px 60px rgba(23,51,91,.05);">
                <div class="card-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:18px; margin-bottom:24px;">
                    <div>
                        <h2 style="font-size:22px; margin:0; color:#172f56;">User Directory</h2>
                        <p style="margin:8px 0 0; color:#5f728f; line-height:1.7;">Browse active and inactive administrators and their roles.</p>
                    </div>
                    <div class="table-action-row">
                        @include('components.datatable', ['tableId' => 'users-table'])
                    </div>
                </div>

                <div class="table-card" style="overflow:auto; margin-top:20px;">
                    @php($canUpdate = \App\Helpers\PermissionHelper::check('users', 'update'))
                    @php($canDelete = \App\Helpers\PermissionHelper::check('users', 'delete'))
                    <table id="users-table" class="matrix" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f6f9fc;">
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">#</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Name</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Email</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Designation</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Role</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Is Active</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Created At</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Updated At</th>
                                @if($canUpdate || $canDelete)
                                    <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $u)
                                <tr>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $index + 1 }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254; font-weight:700;">{{ $u->name }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $u->email }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $u->designation ?? '—' }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;"><span class="footer-badge" style="display:inline-block; padding:4px 8px; border-radius:20px; color:#1761bc; background:#e7f2ff; font-size:10px; font-weight:800;">{{ $u->role?->name ?? '—' }}</span></td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <label class="switch" style="position:relative; display:inline-block; width:56px; height:30px;">
                                            <input type="checkbox" class="user-toggle-switch" data-id="{{ $u->id }}" {{ $u->is_active ? 'checked' : '' }} aria-label="Toggle user status" style="opacity:0; width:0; height:0;">
                                            <span class="slider" style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#e6eef8; transition:.3s; border-radius:30px; border:1px solid #e6eef8;"></span>
                                        </label>
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $u->created_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $u->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $u->updated_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $u->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    @if($canUpdate || $canDelete)
                                        <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                            @if($canUpdate)
                                                <a href="{{ route('menus.create', ['menu' => $menu->slug, 'edit' => $u->id]) }}" class="action-button edit-button" style="text-decoration:none; padding:6px 12px; background:#e7f2ff; color:#1761bc; border-radius:8px; font-weight:700; font-size:12px; margin-right:8px;">Edit</a>
                                            @endif
                                            @if($canDelete)
                                                <form method="POST" action="{{ route('menus.users.destroy', $u) }}" style="display:inline-block" onsubmit="return confirm('Delete this user?')">
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
                                    <td colspan="{{ $canUpdate || $canDelete ? 9 : 8 }}" style="padding:30px; text-align:center; color:#6d7e9c; font-size:14px;">No users found. Use the module action to add new entries.</td>
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
<!-- Toastr notifications -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ custom_asset('js/menus/users.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Simple style injection for the toggle switch slider inside users table
        const style = document.createElement('style');
        style.textContent = `
            .switch input:checked + .slider { background: #1761bc; border-color: #1761bc; }
            .switch input:checked + .slider:before { transform: translateX(26px); }
            .switch .slider:before { position: absolute; content: ""; height: 24px; width: 24px; left: 3px; top: 2px; background: #fff; transition: .3s; border-radius: 50%; box-shadow: 0 2px 6px rgba(16,46,80,0.12); }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush