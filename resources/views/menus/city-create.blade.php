@extends('layouts.app')

@section('title', ($editingCity ? 'Update City' : 'Create City') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/city-create.css') }}">
<style>
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
    .role-form-inner select {
        width: 100%;
        height: 48px;
        border: 1.5px solid #dcdfe6;
        border-radius: 12px;
        padding: 0 16px;
        font: 500 14px Inter,Arial,sans-serif;
        color: #1f3254;
        background: #fff;
        outline: none;
        transition: border-color .2s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%23606266' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }
    .role-form-inner select:focus {
        border-color: #1761bc;
    }
    .map-preview-container {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        height: 250px;
        margin-bottom: 24px;
        background: #f7fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .map-placeholder-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.85;
    }
    .map-overlay-card {
        position: absolute;
        background: rgba(255, 255, 255, 0.95);
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 320px;
    }
    .map-overlay-card h4 {
        margin: 0 0 4px;
        color: #2d3748;
        font-size: 15px;
        font-weight: 700;
    }
    .map-overlay-card p {
        margin: 0;
        color: #718096;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> City <span>›</span> <b>{{ $editingCity ? 'Edit' : 'Create' }}</b></div>
            <h1 class="page-heading">{{ $editingCity ? 'Update City' : 'Create City' }}</h1>
            <p class="page-intro">{{ $editingCity ? 'Modify existing city descriptions, meta tags, banners, and options.' : 'Fill in the details below to add a new City record.' }}</p>

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
                        <h2>City details</h2>
                        <p>Provide region associations, descriptions, SEO configurations, and images.</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="{{ $editingCity ? route('menus.cities.update', $editingCity) : route('menus.cities.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($editingCity)
                        @method('PUT')
                    @endif
                    
                    {{-- Row 1: Country and State --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="country_id">Country</label>
                            <select id="country_id" name="country_id" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $editingCity?->country_id) == $country->id)>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="state_id">State</label>
                            <select id="state_id" name="state_id" required>
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" data-country-id="{{ $state->country_id }}" @selected(old('state_id', $editingCity?->state_id) == $state->id)>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: City Name and Slug --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="name">City Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $editingCity?->name) }}" placeholder="Enter city name" required>
                        </div>
                        <div class="field">
                            <label for="slug">Slug</label>
                            <input id="slug" name="slug" type="text" value="{{ old('slug', $editingCity?->slug) }}" placeholder="Enter slug" required>
                        </div>
                    </div>

                    {{-- Row 3: Time To Visit and Currency --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="time_to_visit">Time To Visit</label>
                            <input id="time_to_visit" name="time_to_visit" type="text" value="{{ old('time_to_visit', $editingCity?->time_to_visit) }}" placeholder="Best time to visit (e.g. Oct to Mar)">
                        </div>
                        <div class="field">
                            <label for="currency">Currency</label>
                            <input id="currency" name="currency" type="text" value="{{ old('currency', $editingCity?->currency) }}" placeholder="Currency">
                        </div>
                    </div>

                    {{-- Row 4: Language --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="language">Language</label>
                            <input id="language" name="language" type="text" value="{{ old('language', $editingCity?->language) }}" placeholder="Languages spoken">
                        </div>
                    </div>

                    {{-- Row 5: City Introduction --}}
                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="introduction">City Introduction</label>
                            <textarea id="introduction" name="introduction" style="height:120px; border:1.5px solid #dcdfe6; border-radius:12px; padding:12px 16px; font-family:inherit; outline:none;" placeholder="Enter short city introduction">{{ old('introduction', $editingCity?->introduction) }}</textarea>
                        </div>
                    </div>

                    {{-- Row 6: Name to get lat log and Address --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="lat_log_name">Name to get lat log</label>
                            <input id="lat_log_name" name="lat_log_name" type="text" value="{{ old('lat_log_name', $editingCity?->lat_log_name) }}" placeholder="Name to get coordinates">
                        </div>
                        <div class="field">
                            <label for="address">Address</label>
                            <input id="address" name="address" type="text" value="{{ old('address', $editingCity?->address) }}" placeholder="Address">
                        </div>
                    </div>

                    {{-- Row 7: Latitude and Longitude --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="latitude">Latitude</label>
                            <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $editingCity?->latitude) }}" placeholder="Latitude">
                        </div>
                        <div class="field">
                            <label for="longitude">Longitude</label>
                            <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $editingCity?->longitude) }}" placeholder="Longitude">
                        </div>
                    </div>

                    {{-- Row 9: Description (TinyMCE) --}}
                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Detailed Description">{{ old('description', $editingCity?->description) }}</textarea>
                        </div>
                    </div>

                    {{-- Row 10: SEO Title and Meta Keyword --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="seo_title">SEO Title</label>
                            <input id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', $editingCity?->seo_title) }}" placeholder="Meta SEO Title">
                        </div>
                        <div class="field">
                            <label for="meta_keyword">Meta Keyword</label>
                            <input id="meta_keyword" name="meta_keyword" type="text" value="{{ old('meta_keyword', $editingCity?->meta_keyword) }}" placeholder="Meta Keywords (comma separated)">
                        </div>
                    </div>

                    {{-- Row 11: Meta Description --}}
                    <div class="form-row">
                        <div class="field" style="flex:1 1 100%;">
                            <label for="meta_description">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" style="height:100px; border:1.5px solid #dcdfe6; border-radius:12px; padding:12px 16px; font-family:inherit; outline:none;" placeholder="Enter meta description">{{ old('meta_description', $editingCity?->meta_description) }}</textarea>
                        </div>
                    </div>

                    {{-- Row 12: City Banner Image and City Thumb Image --}}
                    <div class="form-row">
                        <div class="field">
                            <label for="banner_image">City Banner Image</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="banner_image" name="banner_image" accept="image/*">
                                <div class="file-input-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    CHOOSE FILE
                                </div>
                                <div class="file-input-name" id="banner-file-name">
                                    @if($editingCity && $editingCity->banner_image)
                                        {{ basename($editingCity->banner_image) }}
                                    @else
                                        No file chosen
                                    @endif
                                </div>
                            </div>
                            @if($editingCity && $editingCity->banner_image)
                                <div style="margin-top: 8px;">
                                    <img src="{{ custom_upload($editingCity->banner_image) }}" alt="Banner" style="height: 48px; border-radius: 6px;">
                                </div>
                            @endif
                        </div>
                        <div class="field">
                            <label for="thumb_image">City Thumb Image</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="thumb_image" name="thumb_image" accept="image/*">
                                <div class="file-input-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    CHOOSE FILE
                                </div>
                                <div class="file-input-name" id="thumb-file-name">
                                    @if($editingCity && $editingCity->thumb_image)
                                        {{ basename($editingCity->thumb_image) }}
                                    @else
                                        No file chosen
                                    @endif
                                </div>
                            </div>
                            @if($editingCity && $editingCity->thumb_image)
                                <div style="margin-top: 8px;">
                                    <img src="{{ custom_upload($editingCity->thumb_image) }}" alt="Thumb" style="height: 48px; border-radius: 6px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Row 13: Our Operation & Is Capital --}}
                    <div class="form-row">
                        <div class="field" style="display:flex; flex-direction:row; align-items:center; gap:30px; margin-top:10px;">
                            <label class="custom-checkbox-field">
                                <input type="checkbox" name="is_capital" value="1" {{ old('is_capital', $editingCity?->is_capital) ? 'checked' : '' }}>
                                <span>Is Capital</span>
                            </label>
                        </div>
                    </div>

                    @if(!$editingCity)
                        <div class="form-row" style="margin-top: 20px;">
                            <div class="checkbox-field">
                                <input id="return_here" name="return_here" type="checkbox" value="1" {{ old('return_here') ? 'checked' : '' }}>
                                <label for="return_here">Return here (Keep this form open after save)</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end" style="display:flex; align-items:center; justify-content:flex-end; gap:16px; margin-top:22px;">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}" style="font:700 14px Inter,Arial,sans-serif; color:#1761bc; text-decoration:none;">Cancel</a>
                        <button class="save" type="submit" style="border:0; background:#1761bc; color:#fff; font:700 14px Inter,Arial,sans-serif; padding:14px 26px; border-radius:14px; cursor:pointer;">{{ $editingCity ? 'Update City' : 'Save City' }}</button>
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
        // Country-State dependent dropdown filtering
        var countrySelect = document.getElementById('country_id');
        var stateSelect = document.getElementById('state_id');
        var allStates = Array.from(stateSelect.options).slice(1).map(function(opt) {
            return {
                id: opt.value,
                name: opt.textContent,
                countryId: opt.dataset.countryId
            };
        });

        function filterStates() {
            var countryId = countrySelect.value;
            stateSelect.innerHTML = '<option value="">Select State</option>';
            allStates.forEach(function(state) {
                if (!countryId || state.countryId == countryId) {
                    var option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    if (state.id == "{{ old('state_id', $editingCity?->state_id) }}") {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                }
            });
        }
        countrySelect.addEventListener('change', filterStates);
        filterStates();

        // Auto-slug generation
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
        var bannerInput = document.getElementById('banner_image');
        var bannerText = document.getElementById('banner-file-name');
        if (bannerInput && bannerText) {
            bannerInput.addEventListener('change', function () {
                if (bannerInput.files.length > 0) {
                    bannerText.textContent = bannerInput.files[0].name;
                } else {
                    bannerText.textContent = "No file chosen";
                }
            });
        }

        var thumbInput = document.getElementById('thumb_image');
        var thumbText = document.getElementById('thumb-file-name');
        if (thumbInput && thumbText) {
            thumbInput.addEventListener('change', function () {
                if (thumbInput.files.length > 0) {
                    thumbText.textContent = thumbInput.files[0].name;
                } else {
                    thumbText.textContent = "No file chosen";
                }
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
            selector: '#description',
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
<script src="{{ custom_asset('js/menus/city-create.js') }}" defer></script>
@endpush
