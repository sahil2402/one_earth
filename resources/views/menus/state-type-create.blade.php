@extends('layouts.app')

@section('title', ($editingStateType ? 'Update State Type' : 'Create State Type') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/state-type-create.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> State Type <span>›</span> <b>{{ $editingStateType ? 'Edit' : 'Create' }}</b></div>
            <h1 class="page-heading">{{ $editingStateType ? 'Update State Type' : 'Create State Type' }}</h1>
            <p class="page-intro">{{ $editingStateType ? 'Modify existing state type classification.' : 'Fill in the details below to add a new State Type record.' }}</p>

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
                        <h2>{{ $editingStateType ? 'Edit State Type Details' : 'Add State Types' }}</h2>
                        <p>{{ $editingStateType ? 'Update state type classifications.' : 'Create a new state type entry.' }}</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="{{ $editingStateType ? route('menus.state-types.update', $editingStateType) : route('menus.state-types.store') }}">
                    @csrf
                    @if($editingStateType)
                        @method('PUT')
                    @endif

                    <div class="form-row">
                        <div class="field" style="flex: 1 1 100%;">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $editingStateType?->name) }}" placeholder="Name" required style="width:100%; height:48px; border:1.5px solid #dcdfe6; border-radius:12px; padding:0 16px; font:500 14px Inter,Arial,sans-serif; color:#1f3254; outline:none; transition:border-color .2s;">
                        </div>
                    </div>

                    @if(!$editingStateType)
                        <div class="form-row" style="margin-top: 20px;">
                            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; user-select:none;">
                                <input type="checkbox" id="return_here" name="return_here" value="1" {{ old('return_here') ? 'checked' : '' }} style="width:18px; height:18px; cursor:pointer;">
                                <span style="font-size:14px; font-weight:600; color:#1f3254;">Return Here</span>
                            </label>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end" style="display:flex; align-items:center; justify-content:flex-end; gap:16px; margin-top:22px;">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}" style="font:700 14px Inter,Arial,sans-serif; color:#1761bc; text-decoration:none;">Cancel</a>
                        <button class="save" type="submit" style="border:0; background:#1761bc; color:#fff; font:700 14px Inter,Arial,sans-serif; padding:14px 26px; border-radius:14px; cursor:pointer;">{{ $editingStateType ? 'Update' : 'SUBMIT' }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ custom_asset('js/menus/state-type-create.js') }}" defer></script>
@endpush
