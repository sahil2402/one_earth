@extends('layouts.app')

@section('title', ($editingState ? 'Update State' : 'Create State') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/state-create.css') }}">
<style>
    /* Styling elements specific to state-create form layout */
    .checkbox-container-row {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-top: 15px;
    }
    .custom-checkbox-field {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }
    .custom-checkbox-field input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .custom-checkbox-field span {
        font-size: 14px;
        font-weight: 600;
        color: #1f3254;
    }
    .file-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        border: 1.5px solid #dcdfe6;
        border-radius: 12px;
        padding: 4px;
        background: #fff;
    }
    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    .file-input-btn {
        background: #3b82f6;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .file-input-name {
        margin-left: 12px;
        font-size: 13px;
        color: #606266;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> State <span>›</span> <b>{{ $editingState ? 'Edit' : 'Create' }}</b></div>
            <h1 class="page-heading">{{ $editingState ? 'Update State' : 'Create State' }}</h1>
            <p class="page-intro">{{ $editingState ? 'Modify existing state attributes, coordinates, operation coverage, and files.' : 'Fill in the details below to add a new State record to the system.' }}</p>

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
                <div class="card-header" style="margin-bottom:24px;">
                    <div>
                        <h2>State details</h2>
                        <p>Provide region properties, coordinates, and options.</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="{{ $editingState ? route('menus.states.update', $editingState) : route('menus.states.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($editingState)
                        @method('PUT')
                    @endif
                    
                    {{-- Row 1: Country and State Type --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="country_id">Country</label>
                            <select id="country_id" name="country_id" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $editingState?->country_id) == $country->id)>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="state_type">State Type</label>
                            <select id="state_type" name="state_type" required>
                                <option value="">Select State Type</option>
                                @foreach($state_types as $st)
                                    <option value="{{ $st->name }}" @selected(old('state_type', $editingState?->state_type) === $st->name)>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: State Name and Slug --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="name">State Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $editingState?->name) }}" placeholder="Enter state name" required>
                        </div>
                        <div class="field">
                            <label for="slug">Slug</label>
                            <input id="slug" name="slug" type="text" value="{{ old('slug', $editingState?->slug) }}" placeholder="Enter slug (e.g. maharashtra)" required>
                        </div>
                    </div>

                    {{-- Row 3: Choose File, Our Operation, Is Capital --}}
                    <div class="form-row" style="align-items: center;">
                        <div class="field">
                            <label for="image_path">Choose File (State Image)</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="image_path" name="image_path" accept="image/*">
                                <div class="file-input-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    CHOOSE FILE
                                </div>
                                <div class="file-input-name" id="file-name-text">
                                    @if($editingState && $editingState->image_path)
                                        {{ basename($editingState->image_path) }}
                                    @else
                                        No file chosen
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="field" style="display:flex; flex-direction:row; align-items:center; gap:30px; margin-top:28px;">
                            <label class="custom-checkbox-field">
                                <input type="checkbox" name="is_capital" value="1" {{ old('is_capital', $editingState?->is_capital) ? 'checked' : '' }}>
                                <span>Is Capital</span>
                            </label>
                        </div>
                    </div>

                    {{-- Row 4: Name to get lat log and Address --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="lat_log_name">Name to get lat log</label>
                            <input id="lat_log_name" name="lat_log_name" type="text" value="{{ old('lat_log_name', $editingState?->lat_log_name) }}" placeholder="Name to get coordinates">
                        </div>
                        <div class="field">
                            <label for="address">Address</label>
                            <input id="address" name="address" type="text" value="{{ old('address', $editingState?->address) }}" placeholder="Address">
                        </div>
                    </div>

                    {{-- Row 5: Latitude and Longitude --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="latitude">Latitude</label>
                            <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $editingState?->latitude) }}" placeholder="Latitude">
                        </div>
                        <div class="field">
                            <label for="longitude">Longitude</label>
                            <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $editingState?->longitude) }}" placeholder="Longitude">
                        </div>
                    </div>

                    @if(!$editingState)
                        <div class="form-row" style="margin-top: 20px;">
                            <div class="checkbox-field">
                                <input id="return_here" name="return_here" type="checkbox" value="1" {{ old('return_here') ? 'checked' : '' }}>
                                <label for="return_here">Return here (Keep this form open after save)</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end" style="display:flex; align-items:center; justify-content:flex-end; gap:16px; margin-top:22px;">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}" style="font:700 14px Inter,Arial,sans-serif; color:#1761bc; text-decoration:none;">Cancel</a>
                        <button class="save" type="submit" style="border:0; background:#1761bc; color:#fff; font:700 14px Inter,Arial,sans-serif; padding:14px 26px; border-radius:14px; cursor:pointer;">{{ $editingState ? 'Update State' : 'Save State' }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-slug generation from state name
        var nameInput = document.getElementById('name');
        var slugInput = document.getElementById('slug');
        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function () {
                if (!slugInput.dataset.edited) {
                    slugInput.value = nameInput.value
                        .toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                }
            });
            slugInput.addEventListener('input', function () {
                slugInput.dataset.edited = "true";
            });
        }

        // Show uploaded file name
        var fileInput = document.getElementById('image_path');
        var fileText = document.getElementById('file-name-text');
        if (fileInput && fileText) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files.length > 0) {
                    fileText.textContent = fileInput.files[0].name;
                } else {
                    fileText.textContent = "No file chosen";
                }
            });
        }
    });
</script>
<script src="{{ custom_asset('js/menus/state-create.js') }}" defer></script>
@endpush
