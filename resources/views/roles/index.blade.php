@extends('layouts.app')

@section('title', 'Role & Permission | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<!-- Toastr CSS for consistent notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')

    <div class="app-main">
        @include('components.dashboard.navbar')

        <main class="page">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <section class="card page-header-card">
                <div>
                    <p class="page-tag">Settings <span>›</span> Role & Permission</p>
                    <h1>Role & Permission</h1>
                    <p class="intro">Create roles and define module-level create, update and delete access for each workspace module.</p>
                </div>
                <button id="toggle-role-form" type="button" class="primary-button">{{ $editingRole || old('name') ? 'Hide form' : 'Add New Role' }}</button>
            </section>

            <div class="page-grid">
                <section id="role-form-card" class="card role-form-card {{ $editingRole || old('name') ? '' : 'collapsed' }}">
                    <div class="card-header">
                        <div>
                            <h2>{{ $editingRole ? 'Update role' : 'Add Role' }}</h2>
                            <p>{{ $editingRole ? 'Update role permissions.' : 'Create a new role and configure permissions.' }}</p>
                        </div>
                    </div>

                    <div id="role-form-container" class="role-form-inner">
                        <form method="POST" action="{{ $editingRole ? route('roles.update', $editingRole) : route('roles.store') }}">
                            @csrf
                            @if($editingRole)
                                @method('PUT')
                            @endif

                            <div class="form-row">
                                <div class="field">
                                    <label for="name">Role name</label>
                                    <input id="name" name="name" value="{{ old('name', $editingRole?->name) }}" placeholder="Enter role name" required>
                                    @error('name')
                                        <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="field">
                                    <label>Status</label>
                                    <div class="status-options">
                                        <label class="status-option">
                                            <input name="is_active" type="radio" value="1" @checked(old('is_active', $editingRole?->is_active ?? true))>
                                            <span>Active</span>
                                        </label>
                                        <label class="status-option">
                                            <input name="is_active" type="radio" value="0" @checked(old('is_active', $editingRole?->is_active ?? true) === false)>
                                            <span>Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="permissions-box">
                                <p class="section-label">Module permissions</p>
                                <table class="matrix">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>View</th>
                                            <th>Create</th>
                                            <th>Update</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($menus as $module)
                                            @php($existing = $editingRole?->permissions->firstWhere('menu_id', $module->id))
                                            <tr>
                                                <td>{{ $module->name }}</td>
                                                <td><input class="permission" type="checkbox" name="permissions[{{ $module->id }}][view]" value="1" @checked(old("permissions.{$module->id}.view", $existing?->can_view))></td>
                                                <td><input class="permission" type="checkbox" name="permissions[{{ $module->id }}][create]" value="1" @checked(old("permissions.{$module->id}.create", $existing?->can_create))></td>
                                                <td><input class="permission" type="checkbox" name="permissions[{{ $module->id }}][update]" value="1" @checked(old("permissions.{$module->id}.update", $existing?->can_update))></td>
                                                <td><input class="permission" type="checkbox" name="permissions[{{ $module->id }}][delete]" value="1" @checked(old("permissions.{$module->id}.delete", $existing?->can_delete))></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-actions form-actions-end">
                                @if($editingRole)
                                    <a class="cancel" href="{{ route('roles.index') }}">Cancel</a>
                                @endif
                                <button class="save" type="submit">{{ $editingRole ? 'Update role' : 'Create role' }}</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <section class="card roles-table-card">
                <div class="card-header">
                    <div>
                        <h2>Existing roles</h2>
                        <p>Manage active and inactive access roles and view audit details.</p>
                    </div>
                    <div class="table-action-row">
                        @include('components.datatable', ['tableId' => 'roles-table'])
                    </div>
                </div>

                <div class="table-card">
                    <table id="roles-table">
                        <thead>
                            <tr>
                                <th><button type="button" class="sort-header" data-sort-column="0">Name <span class="sort-icon">↕</span></button></th>
                                <th><button type="button" class="sort-header" data-sort-column="1">Status <span class="sort-icon">↕</span></button></th>
                                <th>Created by</th>
                                <th>Updated by</th>
                                <th><button type="button" class="sort-header" data-sort-column="4" data-sort-type="date">Created at <span class="sort-icon">↕</span></button></th>
                                <th><button type="button" class="sort-header" data-sort-column="5" data-sort-type="date">Updated at <span class="sort-icon">↕</span></button></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <label class="status-toggle">
                                            <input id="status-{{ $item->id }}" type="checkbox" class="status-switch" data-url="{{ route('roles.toggleStatus', $item) }}" @checked($item->is_active)>
                                            <span class="switch-track"></span>
                                            <span class="status-label {{ $item->is_active ? 'active' : 'inactive' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                                        </label>
                                    </td>
                                    <td>{{ $item->created_by ?? '—' }}</td>
                                    <td>{{ $item->updated_by ?? '—' }}</td>
                                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                                    <td class="actions">
                                        <a class="action-button edit-button" href="{{ route('roles.index', ['edit' => $item->id]) }}">Edit</a>
                                        <form method="POST" action="{{ route('roles.destroy', $item) }}" onsubmit="return confirm('Delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="action-button delete-button" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<div id="toast-container" class="toast-container" aria-live="polite"></div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-3fp9K2G0f8z4boa3cKAZ+fx7F8HkgPWdIr2UjE6Cp8c=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-vKM6xqSv7Ilb6dYqV1U+z5vUMr7LZy/fbKJWnSHPv9d1C75c7NfJxoe+m/OgJ2Tn7EyEQhN5dPd7P6/58m8S2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
</div>
@endsection
