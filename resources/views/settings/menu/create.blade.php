@extends('layouts.app')

@section('title', 'Create Menu | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
/* Make the container span the content width and look like a card */
.menu-container{
    width:100%;
    margin:18px 0;
    padding:26px;
    background:#fff;
    border-radius:10px;
    box-shadow:0 6px 18px rgba(16,46,80,0.06);
}
.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}
/* Primary button refined */
.primary-button{
    background:#1760c2;
    color:#fff;
    padding:10px 20px;
    border-radius:12px;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    box-shadow:0 8px 18px rgba(23,96,194,0.12);
    border:0;
    font-weight:700;
}
.primary-button:hover{opacity:0.95}
.muted{color:#7d8da7}
.form-card{display:none;margin-top:14px;border:1px solid #e6eef8;padding:22px;border-radius:10px;background:#ffffff}
.form-card.active{display:block}
.field{margin-bottom:16px}
.field label{display:block;margin-bottom:8px;font-weight:700;color:#0f2a4b}
.field input[type="text"], .field select{width:100%;padding:10px 12px;border:1px solid #e6eef8;border-radius:8px;font-size:14px;color:#122b53}
.type-options{display:flex;gap:18px;margin-top:8px;align-items:flex-start}
.type-option{display:flex;gap:10px;align-items:flex-start}
.type-option input[type="radio"]{margin-top:6px}
.type-option span strong{display:block;font-weight:700}
.type-option span small{display:block;color:#7d8da7;font-weight:600;margin-top:4px}
.form-actions{margin-top:14px;display:flex;gap:12px;align-items:center;justify-content:flex-end}
.table-card{margin-top:20px;border:1px solid #eef4fb;padding:12px;border-radius:10px;background:#ffffff}
table.menu-table{width:100%;border-collapse:collapse}
table.menu-table th, table.menu-table td{padding:14px 12px;border-bottom:1px solid #eef2f7;text-align:left;font-size:13px}
.action-btn{background:#fff;border:1px solid #e1e9f3;padding:6px 8px;border-radius:6px;color:#1760c2;text-decoration:none}
.action-btn:hover{background:#f6fbff}
.toggle-switch{display:inline-flex;align-items:center;gap:8px}
/* Toggle switch styles */
.switch{position:relative;display:inline-block;width:56px;height:30px}
.switch input{opacity:0;width:0;height:0}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#e6eef8;transition:.3s;border-radius:30px;border:1px solid #e6eef8}
.slider:before{position:absolute;content:"";height:24px;width:24px;left:3px;top:2px;background:#fff;transition:.3s;border-radius:50%;box-shadow:0 2px 6px rgba(16,46,80,0.12)}
.switch input:checked + .slider{background:#1760c2;border-color:#1760c2}
.switch input:checked + .slider:before{transform:translateX(26px)}
.switch input:disabled + .slider{opacity:0.6;cursor:not-allowed}
.menu-toggle-btn{display:none}

/* Make table look like a card row */
table.menu-table thead th{background:#f7fbff;color:#6d7e9c;font-weight:700}

@media (min-width: 1200px){
    .menu-container{padding:32px 36px}
}

</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> <b>Menu Create</b></div>
            <h1 class="page-heading">Create Menu</h1>
            <p class="page-intro">Add a top-level menu or place a menu under an existing parent.</p>

            <div class="menu-container">
                <div class="toolbar">
                    <div>
                        <h2 style="margin:0">Menu details</h2>
                        <p class="muted">Choose the menu hierarchy, then save it to your navigation structure.</p>
                    </div>
                    <div>
                        <a href="#" id="add-menu-btn" class="primary-button">{{ $editingMenu ? '- Hide form' : '+ Add menu' }}</a>
                    </div>
                </div>

                @if(session('success'))
                <div class="success">{{ session('success') }}</div>
                @endif

                <section id="menu-form" class="form-card {{ $editingMenu ? 'active' : '' }}">
                    <form method="POST" action="{{ $editingMenu ? route('menu.update', $editingMenu) : route('menu.store') }}">
                        @csrf
                        @if($editingMenu)
                            @method('PUT')
                        @endif
                        <div class="field">
                            <label for="name">Menu name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $editingMenu?->name) }}" placeholder="For example: Bookings" required autofocus>
                            @error('name')<p class="error">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <span class="field-title">Menu placement</span>
                            <div class="type-options">
                                <label class="type-option"><input type="radio" name="menu_type" value="parent" {{ old('menu_type', $editingMenu ? ($editingMenu->parent_id ? 'child' : 'parent') : 'parent') === 'parent' ? 'checked' : '' }} onchange="toggleParentMenu()"><span><strong>Parent menu</strong><small>Appears as a top-level menu item.</small></span></label>
                                <label class="type-option"><input type="radio" name="menu_type" value="child" {{ old('menu_type', $editingMenu ? ($editingMenu->parent_id ? 'child' : 'parent') : 'parent') === 'child' ? 'checked' : '' }} onchange="toggleParentMenu()"><span><strong>Child menu</strong><small>Appears inside an existing parent menu.</small></span></label>
                            </div>
                        </div>

                        <div class="field" id="parent-menu-field" style="display:{{ old('menu_type', $editingMenu ? ($editingMenu->parent_id ? 'child' : 'parent') : 'parent') === 'child' ? 'block' : 'none' }}">
                            <label for="parent_id">Parent menu</label>
                            <select id="parent_id" name="parent_id">
                                <option value="">Select parent menu</option>
                                @foreach($parentMenus as $parentMenu)
                                    <option value="{{ $parentMenu->id }}" @selected(old('parent_id', $editingMenu?->parent_id) == $parentMenu->id)>{{ $parentMenu->name }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')<p class="error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-actions">
                            <a class="action-btn" href="{{ route('menu.create') }}">Cancel</a>
                            <button class="primary-button" type="submit">{{ $editingMenu ? 'Update menu' : 'Create menu' }}</button>
                        </div>
                    </form>
                </section>

                <section class="table-card">
                    <table class="menu-table" id="menus-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Created By</th>
                                <th>Updated By</th>
                                <th>Is Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $i => $m)
                            <tr data-id="{{ $m->id }}">
                                <td>{{ $i+1 }}</td>
                                <td>{{ $m->name }}</td>
                                <td>{{ $m->parent?->name ?? '—' }}</td>
                                <td>{{ $m->created_by ?? '—' }}</td>
                                <td>{{ $m->updated_by ?? '—' }}</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" class="menu-toggle-switch" data-id="{{ $m->id }}" {{ $m->is_active ? 'checked' : '' }} aria-label="Toggle active">
                                        <span class="slider"></span>
                                    </label>
                                </td>
                                <td>
                                    <a href="{{ route('menu.create', ['edit' => $m->id]) }}" class="action-btn edit-menu">Edit</a>
                                    <form method="POST" action="{{ route('menu.destroy', $m) }}" style="display:inline-block" onsubmit="return confirm('Delete this menu?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="action-btn" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Toastr notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// configure toastr
toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "2200",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
};

function toggleParentMenu() {
    const selected = document.querySelector('input[name="menu_type"]:checked');
    const parentField = document.getElementById('parent-menu-field');
    if (parentField) {
        parentField.style.display = selected && selected.value === 'child' ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // initialize DataTable
    if (window.jQuery && $.fn.dataTable) {
        $('#menus-table').DataTable({
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: [5,6] },
            ]
        });
    }

    const addBtn = document.getElementById('add-menu-btn');
    const formCard = document.getElementById('menu-form');

    if (addBtn && formCard) {
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();
            formCard.classList.toggle('active');
            if (formCard.classList.contains('active')) {
                addBtn.textContent = '- Hide form';
            } else {
                addBtn.textContent = '+ Add menu';
            }
        });
    }
});
</script>
@endpush

@push('scripts')

@endpush
