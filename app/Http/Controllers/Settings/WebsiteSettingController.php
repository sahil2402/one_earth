<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    public function edit(Request $request): View
    {
        $setting = WebsiteSetting::first();

        return view('settings.website', [
            'setting' => $setting,
            'role' => session('authenticated_role'),
            'email' => session('authenticated_email'),
            'navbarTitle' => 'Website Settings',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'zoom_level' => ['required', 'string', 'max:10'],
        ]);

        $setting = WebsiteSetting::first() ?? new WebsiteSetting();
        $setting->site_name = $data['site_name'];
        $setting->zoom_level = $data['zoom_level'];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads');
            if (!is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
            $file->move($dest, $filename);
            $setting->logo_path = 'uploads/' . $filename;
        }

        $setting->save();

        return redirect()->route('settings.website')->with('success', 'Website settings saved.');
    }
}
