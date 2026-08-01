@extends('layouts.app')

@section('title', ($editingDomain ? 'Update Domain' : 'Create Domain') . ' | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ custom_asset('css/menus/domain-create.css') }}">
<style>
    .checkbox-container-row {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-top: 15px;
    }
    .custom-checkbox-field {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #172f56;
        cursor: pointer;
        user-select: none;
    }
    .custom-checkbox-field input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #1761bc;
        cursor: pointer;
    }
    .file-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        gap: 15px;
        background: #fff;
        border: 1.5px solid #dce4ee;
        border-radius: 12px;
        padding: 10px 16px;
        margin-top: 6px;
        transition: all 0.2s ease;
    }
    .file-input-wrapper:hover {
        border-color: #1761bc;
    }
    .file-input-wrapper input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .file-input-btn {
        background: #edf4fc;
        color: #1761bc;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .02em;
        pointer-events: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .file-input-name {
        font-size: 13px;
        color: #5d6c88;
        font-weight: 600;
        pointer-events: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page">
            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif

            <section class="card page-header-card">
                <div>
                    <p class="page-tag">Settings <span>›</span> <a href="{{ route('menus.show', $menu) }}" style="text-decoration:none; color:#5f728f;">Domain</a> <span>›</span> {{ $editingDomain ? 'Update' : 'Create' }}</p>
                    <h1>{{ $editingDomain ? 'Update Domain' : 'Create Domain' }}</h1>
                    <p class="intro">Fill in the domains and SMTP specifications below.</p>
                </div>
            </section>

            <section class="card role-form-card" style="background:#fff; border:1px solid #e6edf6;padding:32px; box-shadow:0 20px 60px rgba(23,51,91,.05); margin-top:20px;">
                <form class="role-form-inner" method="POST" action="{{ $editingDomain ? route('menus.domains.update', $editingDomain) : route('menus.domains.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($editingDomain)
                        @method('PUT')
                    @endif

                    {{-- Row 1: Domain Name & SMTP Host --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:18px;">
                        <div class="field">
                            <label for="domain_name" style="font-weight:700; color:#0f224b;">Domain Name <span style="color:#cf3451;">*</span></label>
                            <input id="domain_name" name="domain_name" type="text" value="{{ old('domain_name', $editingDomain?->domain_name) }}" placeholder="example: instatourism.com" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;" required>
                            @error('domain_name') <p style="color:#cf3451; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label for="smtp_host" style="font-weight:700; color:#0f224b;">SMTP Host</label>
                            <input id="smtp_host" name="smtp_host" type="text" value="{{ old('smtp_host', $editingDomain?->smtp_host) }}" placeholder="example: smtp.mailgun.org" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                    </div>

                    {{-- Row 2: SMTP Port & SMTP User --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:18px;">
                        <div class="field">
                            <label for="smtp_port" style="font-weight:700; color:#0f224b;">SMTP Port</label>
                            <input id="smtp_port" name="smtp_port" type="text" value="{{ old('smtp_port', $editingDomain?->smtp_port) }}" placeholder="example: 587" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                        <div class="field">
                            <label for="smtp_user" style="font-weight:700; color:#0f224b;">SMTP User</label>
                            <input id="smtp_user" name="smtp_user" type="text" value="{{ old('smtp_user', $editingDomain?->smtp_user) }}" placeholder="example: postmaster@instatourism.com" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                    </div>

                    {{-- Row 3: SMTP Password & Email From --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:18px;">
                        <div class="field">
                            <label for="smtp_password" style="font-weight:700; color:#0f224b;">SMTP Password</label>
                            <input id="smtp_password" name="smtp_password" type="text" value="{{ old('smtp_password', $editingDomain?->smtp_password) }}" placeholder="example: 39a489bc84d1ad" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                        <div class="field">
                            <label for="email_from" style="font-weight:700; color:#0f224b;">Email From</label>
                            <input id="email_from" name="email_from" type="email" value="{{ old('email_from', $editingDomain?->email_from) }}" placeholder="example: bookings@instatourism.com" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                    </div>

                    {{-- Row 4: Email From Name & Email To Admin User --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:18px;">
                        <div class="field">
                            <label for="email_from_name" style="font-weight:700; color:#0f224b;">Email From Name</label>
                            <input id="email_from_name" name="email_from_name" type="text" value="{{ old('email_from_name', $editingDomain?->email_from_name) }}" placeholder="example: Insta Tourism LLC" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                        <div class="field">
                            <label for="email_to_admin_user" style="font-weight:700; color:#0f224b;">Email To Admin User</label>
                            <input id="email_to_admin_user" name="email_to_admin_user" type="text" value="{{ old('email_to_admin_user', $editingDomain?->email_to_admin_user) }}" placeholder="example: admin@instatourism.com" style="width:100%; border:1.5px solid #dce4ee; border-radius:12px; padding:12px 16px; margin-top:6px; font-size:14px;">
                        </div>
                    </div>

                    {{-- Row 5: Tour Domain Logo (File Upload) --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr; gap:20px; margin-bottom:18px;">
                        <div class="field">
                            <label for="logo_path" style="font-weight:700; color:#0f224b;">Tour Domain Logo</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="logo_path" name="logo_path" accept="image/*">
                                <div class="file-input-btn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    CHOOSE
                                </div>
                                <div class="file-input-name" id="file-name-text">
                                    @if($editingDomain && $editingDomain->logo_path)
                                        {{ basename($editingDomain->logo_path) }}
                                    @else
                                        No file chosen
                                    @endif
                                </div>
                            </div>
                            @if($editingDomain && $editingDomain->logo_path)
                                <div style="margin-top: 8px;">
                                    <img src="{{ custom_upload($editingDomain->logo_path) }}" alt="Logo" style="height: 48px; border-radius: 6px; border:1px solid #e6edf6; padding:4px;">
                                </div>
                            @endif
                            @error('logo_path') <p style="color:#cf3451; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Row 6: Email Header (TinyMCE) - FULL WIDTH --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr; margin-bottom:18px;">
                        <div class="field">
                            <label for="email_header" style="font-weight:700; color:#0f224b; display:block; margin-bottom:6px;">Email Header</label>
                            <textarea id="email_header" name="email_header" placeholder="Enter custom email header content">@if($editingDomain){{ old('email_header', $editingDomain->email_header) }}@else{{ old('email_header', '<div style="background-color: #0c2b5e; color: #ffffff; padding: 20px; border-radius: 8px; font-family: Verdana, sans-serif; margin-bottom: 20px; text-align: center;"><h1 style="margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">One Earth Holidays</h1><p style="margin: 5px 0 0 0; font-size: 13px; color: #cbd5e1; font-weight: 600;">Your Premium Travel Companion</p></div>') }}@endif</textarea>
                        </div>
                    </div>

                    {{-- Row 7: Email Footer (TinyMCE) - FULL WIDTH --}}
                    <div class="form-row" style="display:grid; grid-template-columns:1fr; margin-bottom:18px;">
                        <div class="field">
                            <label for="email_footer" style="font-weight:700; color:#0f224b; display:block; margin-bottom:6px;">Email Footer</label>
                            <textarea id="email_footer" name="email_footer" placeholder="Enter custom email footer content">@if($editingDomain){{ old('email_footer', $editingDomain->email_footer) }}@else{{ old('email_footer', '<div style="background-color: #0c2b5e; color: #ffffff; padding: 25px; border-radius: 8px; text-align: center; font-family: Verdana, sans-serif;"><h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">Contact Us</h3><h2 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 800; text-transform: uppercase;">One Earth Holidays</h2><p style="margin: 0 0 18px 0; font-size: 13px; line-height: 1.5; color: #e2e8f0;">Jaipur, UAE WhatsApp: +971-1111111111</p><div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 15px; margin-top: 15px;"><div style="text-align: left;"><h4 style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; color: #cbd5e1; text-transform: uppercase;">Get In Touch</h4><div style="font-size: 12px; color: #e2e8f0; font-weight: 600;">📸 Instagram | 👥 Facebook | 🐦 Twitter</div></div><div style="text-align: right;"><h4 style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; color: #cbd5e1; text-transform: uppercase;">Download Our App</h4><div style="font-size: 12px; color: #e2e8f0; font-weight: 600;">📱 App Store | 🤖 Google Play</div></div></div></div>') }}@endif</textarea>
                        </div>
                    </div>

                    @if(!$editingDomain)
                        <div class="form-row" style="margin-top: 20px; display:block;">
                            <div class="checkbox-field">
                                <input id="return_here" name="return_here" type="checkbox" value="1" {{ old('return_here') ? 'checked' : '' }}>
                                <label for="return_here" style="font-weight:700; color:#172f56; cursor:pointer;">Return here (Keep this form open after save)</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions form-actions-end" style="display:flex; align-items:center; justify-content:flex-end; gap:16px; margin-top:28px;">
                        <a class="cancel" href="{{ route('menus.show', $menu) }}" style="font:700 14px Inter,Arial,sans-serif; color:#1761bc; text-decoration:none; padding:12px 24px;">Cancel</a>
                        <button class="save" type="submit" style="border:0; background:#1761bc; color:#fff; font:700 14px Inter,Arial,sans-serif; padding:14px 26px; border-radius:14px; cursor:pointer;">{{ $editingDomain ? 'Update Domain' : 'Submit' }}</button>
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
        // Show uploaded file name
        var fileInput = document.getElementById('logo_path');
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
<!-- TinyMCE CDN and init -->
<script src="https://cdn.tiny.cloud/1/tm6v9o9br25gnfmykn9epfketmaqtickl9b88ewomqq6dg7x/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.tinymce) {
        tinymce.init({
            selector: '#email_header, #email_footer',
            height: 400,
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
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            callback(blobInfo.blobUri(), { title: file.name });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            }
        });
    }
});
</script>
<script src="{{ custom_asset('js/menus/domain-create.js') }}" defer></script>
@endpush
