@extends('layouts.app')

@section('title', ($editingUser ? 'Update User' : 'Create User') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/users-create.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> Users <span>›</span> <b>{{ $editingUser ? 'Edit' : 'Create' }}</b></div>
            <h1 class="page-heading">{{ $editingUser ? 'Update User' : 'Create User' }}</h1>
            <p class="page-intro">{{ $editingUser ? 'Modify user credentials, contact details, and role permissions.' : 'Fill in the details below to add a new administrative user to this workspace.' }}</p>

            @if(session('success'))
                <div class="alert alert-success" style="padding:14px 18px; margin:20px 0; border:1px solid #d3e1ff; border-radius:14px; background:#eef4ff; color:#1246a0;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="padding:14px 18px; margin:20px 0; border:1px solid #f8d7da; border-radius:14px; background:#f8d7da; color:#721c24;">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="card role-form-card" style="margin-top: 20px;">
                <div class="card-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:18px; margin-bottom:24px;">
                    <div>
                        <h2>User details</h2>
                        <p>Provide contact and access credentials for the user account.</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="{{ $editingUser ? route('menus.users.update', $editingUser) : route('menus.users.store') }}">
                    @csrf
                    @if($editingUser)
                        @method('PUT')
                    @endif
                    
                    <div class="form-row">
                        <div class="field">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $editingUser?->name) }}" placeholder="Enter full name" required autofocus>
                        </div>
                        <div class="field">
                            <label for="email">Email Address</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $editingUser?->email) }}" placeholder="Enter email address" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="password">Password {{ $editingUser ? '(Leave blank to keep current)' : '' }}</label>
                            <input id="password" name="password" type="password" placeholder="{{ $editingUser ? 'Enter new password or leave blank' : 'Enter password' }}" {{ $editingUser ? '' : 'required' }}>
                        </div>
                        <div class="field">
                            <label for="designation">Designation</label>
                            <input id="designation" name="designation" type="text" value="{{ old('designation', $editingUser?->designation) }}" list="designations" placeholder="Enter designation (e.g. Sales Executive)">
                            <datalist id="designations">
                                @foreach($designations as $designation)
                                    <option value="{{ $designation }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="role_id">Role & Permission</label>
                            <select id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $roleItem)
                                    <option value="{{ $roleItem->id }}" @selected(old('role_id', $editingUser?->role_id) == $roleItem->id)>{{ $roleItem->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Status (Is Active)</label>
                            <div style="margin-top: 10px;">
                                <label class="switch">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editingUser ? ($editingUser->is_active ? '1' : '0') : '1') === '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @if(!$editingUser)
                        <div class="form-row" style="margin-top: 20px;">
                            <div class="checkbox-field">
                                <input id="return_here" name="return_here" type="checkbox" value="1" {{ old('return_here') ? 'checked' : '' }}>
                                <label for="return_here">Return here (Keep this form open after save)</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end" style="display:flex; align-items:center; justify-content:flex-end; gap:16px; margin-top:22px;">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}" style="font:700 14px Inter,Arial,sans-serif; color:#1761bc; text-decoration:none;">Cancel</a>
                        <button class="save" type="submit" style="border:0; background:#1761bc; color:#fff; font:700 14px Inter,Arial,sans-serif; padding:14px 26px; border-radius:14px; cursor:pointer;">{{ $editingUser ? 'Update User' : 'Save User' }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ custom_asset('js/menus/users-create.js') }}" defer></script>
@endpush
