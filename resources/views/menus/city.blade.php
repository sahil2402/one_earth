@extends('layouts.app')

@section('title', 'City Management | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/city.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success" style="padding:14px 18px; margin-bottom:20px; border:1px solid #d3e1ff; border-radius:14px; background:#eef4ff; color:#1246a0;">
                    {{ session('success') }}
                </div>
            @endif

            <section class="card page-header-card" style="display:flex; align-items:flex-start; justify-content:space-between; gap:24px; margin-bottom:24px; min-height:auto; padding:28px;">
                <div>
                    <div class="breadcrumb" style="margin:0 0 10px;">Settings <span>›</span> <b>City</b></div>
                    <h1 class="page-heading" style="margin:0 0 6px; font-size:34px;">Cities</h1>
                    <p class="page-intro" style="margin:0; color:#7184a4; font-size:14px;">Manage regional cities, their countries, states, currency, coordinates, and rich descriptions.</p>
                </div>
                @if (\App\Helpers\PermissionHelper::check($menu->slug, 'create'))
                    <a href="{{ route('menus.create', $menu) }}" class="primary-button">+ Add City</a>
                @endif
            </section>

            <section class="card roles-table-card" style="background:#fff; border:1px solid #e6edf6; border-radius:24px; padding:28px; box-shadow:0 20px 60px rgba(23,51,91,.05);">
                <div class="card-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:18px; margin-bottom:24px;">
                    <div>
                        <h2 style="font-size:22px; margin:0; color:#172f56;">City Directory</h2>
                        <p style="margin:8px 0 0; color:#5f728f; line-height:1.7;">Browse active cities, operation status, and region associations.</p>
                    </div>
                    <div class="table-action-row">
                        @include('components.datatable', ['tableId' => 'cities-table'])
                    </div>
                </div>

                <div class="table-card" style="overflow:auto; margin-top:20px;">
                    @php($canUpdate = \App\Helpers\PermissionHelper::check('city', 'update'))
                    @php($canDelete = \App\Helpers\PermissionHelper::check('city', 'delete'))
                    <table id="cities-table" class="matrix" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f6f9fc;">
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">#</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">City Name</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Slug</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">State</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Country</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Is Capital</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Created At</th>
                                <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Updated At</th>
                                @if($canUpdate || $canDelete)
                                    <th style="padding:16px 14px; text-align:left; border-bottom:1px solid #e7eef7; font-size:12px; font-weight:800; color:#5d6c88; text-transform:uppercase; letter-spacing:.08em;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cities as $index => $c)
                                <tr>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $index + 1 }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254; font-weight:700;">{{ $c->name }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;"><code>{{ $c->slug }}</code></td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $c->state?->name ?? '—' }}</td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">{{ $c->country?->name ?? '—' }}</td>

                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        @if($c->is_capital)
                                            <span style="color:#2b6cb0; background:#ebf8ff; padding:4px 10px; border-radius:12px; font-weight:700; font-size:11px;">Yes</span>
                                        @else
                                            <span style="color:#4a5568; background:#edf2f7; padding:4px 10px; border-radius:12px; font-weight:700; font-size:11px;">No</span>
                                        @endif
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $c->created_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $c->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                        <div style="font-weight:700;">{{ $c->updated_by ?? '—' }}</div>
                                        <div style="font-size:12px; color:#8a99b0; margin-top:2px;">{{ $c->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                    </td>
                                    @if($canUpdate || $canDelete)
                                        <td style="padding:16px 14px; border-bottom:1px solid #e7eef7; font-size:14px; color:#1f3254;">
                                            @if($canUpdate)
                                                <a href="{{ route('menus.create', ['menu' => $menu->slug, 'edit' => $c->id]) }}" class="action-button edit-button" style="text-decoration:none; padding:6px 12px; background:#e7f2ff; color:#1761bc; border-radius:8px; font-weight:700; font-size:12px; margin-right:8px;">Edit</a>
                                            @endif
                                            @if($canDelete)
                                                <form method="POST" action="{{ route('menus.cities.destroy', $c) }}" style="display:inline-block" onsubmit="return confirm('Delete this city?')">
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
                                    <td colspan="{{ $canUpdate || $canDelete ? 9 : 8 }}" style="padding:30px; text-align:center; color:#6d7e9c; font-size:14px;">No cities found. Use the module action to add new entries.</td>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('cities-table-search');
        var table = document.getElementById('cities-table');
        if (searchInput && table) {
            var rows = Array.from(table.tBodies[0]?.rows || []);
            searchInput.addEventListener('input', function () {
                var filter = searchInput.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    if (row.cells.length === 1 && row.cells[0].classList.contains('empty')) {
                        return;
                    }
                    var text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
                });
            });
        }
    });
</script>
<script src="{{ custom_asset('js/menus/city.js') }}" defer></script>
@endpush