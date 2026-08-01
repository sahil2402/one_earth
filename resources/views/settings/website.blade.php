@extends('layouts.app')
@section('title', 'Website Settings | Travel Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<style>
/* Small adjustments for Website Settings layout */
.branding-preview{display:flex;align-items:center;gap:14px;padding:12px;border:1px solid #e6edf6;border-radius:12px;background:#fbfdff}
.branding-preview img{max-height:64px}
.form-actions{justify-content:flex-start}
.save{min-width:140px}
@media(max-width:820px){.form-row{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> <b>Website</b></div>
            <h1 class="page-heading">Website Settings</h1>
            <p class="page-intro">Configure site name and logo displayed on the public site.</p>

            <section class="card role-form-card">
                <div class="card-header">
                    <div>
                        <h2>Branding</h2>
                        <p>Upload a site logo and set the website name.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.website.update') }}" enctype="multipart/form-data" class="role-form-inner">
                    @csrf
                    <div class="form-row">
                        <div class="field">
                            <label for="site_name">Website Name</label>
                            <input id="site_name" name="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" placeholder="Website Name" required>
                        </div>
                        <div class="field">
                            <label for="logo">Website Logo</label>
                            <div class="file-picker">
                                <label class="file-btn" for="logo">Choose file</label>
                                <span class="file-name" id="logo-filename">No file chosen</span>
                                <input id="logo" name="logo" type="file" accept="image/*" style="display:none">
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="align-items:center;margin-top:8px">
                        <div class="field" style="grid-column:1 / -1">
                            <div class="branding-preview">
                                <div>
                                    @if(!empty($setting->logo_path))
                                        <img id="logo-preview" src="{{ custom_upload($setting->logo_path) }}" alt="Logo">
                                    @else
                                        <div id="logo-preview" style="width:64px;height:64px;border-radius:8px;background:#f1f6fb;display:flex;align-items:center;justify-content:center;color:#7d97b8;font-weight:700">LOGO</div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:800;color:#142a54">{{ $setting->site_name ?? 'Website Name' }}</div>
                                    <div style="color:#65768a;font-size:13px">Preview of current website branding</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top:18px">
                        <button class="save" type="submit">Save Settings</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    (function () {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        var fileInput = document.getElementById('logo');
        var fileBtn = document.querySelector('.file-btn');
        var fileName = document.getElementById('logo-filename');
        var preview = document.getElementById('logo-preview');

        if (fileBtn && fileInput) {
            fileBtn.addEventListener('click', function (e) {
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', function () {
                var f = fileInput.files && fileInput.files[0];
                if (!f) {
                    fileName.textContent = 'No file chosen';
                    return;
                }
                fileName.textContent = f.name;

                // show preview
                if (preview && f.type.indexOf('image') === 0) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        if (preview.tagName === 'IMG') {
                            preview.src = e.target.result;
                        } else {
                            // replace placeholder div with an img
                            var img = document.createElement('img');
                            img.id = 'logo-preview';
                            img.src = e.target.result;
                            img.style.maxHeight = '64px';
                            img.style.borderRadius = '8px';
                            preview.replaceWith(img);
                            preview = img;
                        }
                    };
                    reader.readAsDataURL(f);
                }
            });
        }
    })();
</script>
@endpush
