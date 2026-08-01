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
#zoom_level:hover { border-color: #1761bc !important; }
#zoom_level:focus { border-color: #1761bc !important; box-shadow: 0 0 0 3px rgba(23, 97, 188, 0.15) !important; }
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

                    <div class="form-row" style="margin-top: 20px;">
                        <div class="field">
                            <label for="zoom_level" style="font-weight: 700; color: #142a54; margin-bottom: 6px; display: block;">Website Zoom Level</label>
                            <div style="position: relative; width: 100%;">
                                <select id="zoom_level" name="zoom_level" style="width: 100%; height: 50px; padding: 0 45px 0 20px; border: 1.5px solid #dce4ee; border-radius: 12px; font-size: 15px; font-weight: 600; color: #1e2d4a; background: #fff url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%234a5568%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E') no-repeat right 16px center; background-size: 18px; appearance: none; -webkit-appearance: none; cursor: pointer; transition: all 0.2s ease; outline: none;">
                                    @for ($i = 50; $i <= 100; $i += 5)
                                        <option value="{{ $i }}%" {{ old('zoom_level', $setting->zoom_level ?? '100%') === ($i . '%') ? 'selected' : '' }}>
                                            {{ $i }}% {{ $i === 100 ? '(Normal / Default)' : '(Zoom Out)' }}
                                        </option>
                                    @endfor
                                </select>
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
