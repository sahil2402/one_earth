@extends('layouts.app')

@section('title', ($editingCountry ? 'Update Country' : 'Create Country') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/country-create.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> Country <span>›</span> <b>{{ $editingCountry ? 'Edit' : 'Create' }}</b></div>
            <h1 class="page-heading">{{ $editingCountry ? 'Update Country' : 'Create Country' }}</h1>
            <p class="page-intro">{{ $editingCountry ? 'Modify existing country metrics, banner images, coordinates, and options.' : 'Fill in the details below to add a new Country record.' }}</p>

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

            <section class="card role-form-card">
                <div class="card-header">
                    <div>
                        <h2>{{ $editingCountry ? 'Edit Country Details' : 'Add new Country' }}</h2>
                        <p>{{ $editingCountry ? 'Update country fields and configuration.' : 'Create a new Country entry for the workspace.' }}</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="{{ $editingCountry ? route('menus.countries.update', $editingCountry) : route('menus.countries.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($editingCountry)
                        @method('PUT')
                    @endif

                    <div class="form-row">
                        <div class="field">
                            <label for="country_name">Country Name</label>
                            <input id="country_name" name="country_name" value="{{ old('country_name', $editingCountry?->name) }}" placeholder="Country Name" required>
                        </div>
                        <div class="field">
                            <label for="slug">Slug</label>
                            <input id="slug" name="slug" value="{{ old('slug', $editingCountry?->slug) }}" placeholder="Slug" required>
                        </div>
                        <div class="field">
                            <label for="country_code">Country Code (3 Digits)</label>
                            <input id="country_code" name="country_code" value="{{ old('country_code', $editingCountry?->code) }}" placeholder="Country Code (3 digits)" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="address">Address</label>
                            <input id="address" name="address" value="{{ old('address', $editingCountry?->address) }}" placeholder="Address">
                        </div>
                        <div class="field">
                            <label for="latitude">Latitude</label>
                            <input id="latitude" name="latitude" value="{{ old('latitude', $editingCountry?->latitude) }}" placeholder="Latitude">
                        </div>
                        <div class="field">
                            <label for="longitude">Longitude</label>
                            <input id="longitude" name="longitude" value="{{ old('longitude', $editingCountry?->longitude) }}" placeholder="Longitude">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="banner_image">Countries Banner Image</label>
                            <div class="file-chooser">
                                <input type="file" id="banner_image" name="banner_image" accept="image/*">
                            </div>
                            @if($editingCountry && $editingCountry->banner_image)
                                <div style="margin-top: 8px;">
                                    <img src="{{ asset($editingCountry->banner_image) }}" alt="Banner" style="height: 60px; border-radius: 6px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="summary">Summary</label>
                            <textarea id="summary" name="summary" placeholder="Summary">{{ old('summary', $editingCountry?->summary) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Description">{{ old('description', $editingCountry?->description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="iso_code">ISO Code (2 Digits)</label>
                            <input id="iso_code" name="iso_code" value="{{ old('iso_code', $editingCountry?->iso_code) }}" placeholder="ISO Code (2 digits)">
                        </div>
                        <div class="field">
                            <label for="phone_code">Phone Code (2 Digits)</label>
                            <input id="phone_code" name="phone_code" value="{{ old('phone_code', $editingCountry?->phone_code) }}" placeholder="Phone Code (2 digits)">
                        </div>
                        <div class="field">
                            <label for="isd_code">Country ISD Code (With +)</label>
                            <input id="isd_code" name="isd_code" value="{{ old('isd_code', $editingCountry?->isd_code) }}" placeholder="+123">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency">
                                <option value="">Select Option</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="country_currency">Country's Currency</label>
                            <select id="country_currency" name="country_currency">
                                <option value="">Select Option</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="country_type">Country Type</label>
                            <select id="country_type" name="country_type">
                                <option value="">Select Option</option>
                            </select>
                        </div>
                    </div>

                    @if(!$editingCountry)
                        <div class="form-row center-footer">
                            <label style="display:flex;align-items:center;gap:8px;margin:0;">
                                <input type="checkbox" id="stay_here" name="stay_here" value="1" {{ old('stay_here') ? 'checked' : '' }}>
                                <span>Stay Here (Keep this form open after save)</span>
                            </label>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}">Cancel</a>
                        <button class="save" type="submit">{{ $editingCountry ? 'Update Country' : 'Submit' }}</button>
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
        // Auto-slug generation from country name
        var countryNameInput = document.getElementById('country_name');
        var slugInput = document.getElementById('slug');
        if (countryNameInput && slugInput) {
            countryNameInput.addEventListener('input', function () {
                if (!slugInput.dataset.edited) {
                    slugInput.value = countryNameInput.value
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
    });
</script>
<!-- TinyMCE CDN and init -->
<script src="https://cdn.tiny.cloud/1/tm6v9o9br25gnfmykn9epfketmaqtickl9b88ewomqq6dg7x/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.tinymce) {
        tinymce.init({
            selector: '#summary, #description',
            height: 420,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste help wordcount',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | link image media | removeformat | code',
            menubar: 'file edit view insert format tools table help',
            branding: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            paste_as_text: false,
            file_picker_types: 'image media',
            automatic_uploads: true,
            images_reuse_filename: true,
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function () {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.onload = function () {
                            var base64 = reader.result.split(',')[1];
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var id = 'blobid' + (new Date()).getTime();
                            var blobInfo = blobCache.create(id, file, reader.result);
                            blobCache.add(blobInfo);
                            callback(blobInfo.blobUri(), { title: file.name });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            },
            images_upload_handler: function (blobInfo, success, failure) {
                success(blobInfo.blobUri());
            }
        });
    }
});
</script>
<script src="{{ custom_asset('js/menus/country-create.js') }}" defer></script>
@endpush
