<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function create(Request $request): View
    {
        if (!\App\Helpers\PermissionHelper::check('menu-create', 'view')) {
            abort(403, 'Unauthorized.');
        }

        // Settings is the built-in parent for configuration-related child menus.
        Menu::firstOrCreate(
            ['slug' => 'settings'],
            ['name' => 'Settings', 'parent_id' => null],
        );

        return view('settings.menu.create', [
            'role' => session('authenticated_role'),
            'email' => session('authenticated_email'),
            'navbarTitle' => 'Settings',
            'parentMenus' => Menu::query()->whereNull('parent_id')->orderBy('name')->get(),
            'menus' => Menu::with('parent')->orderBy('created_at', 'desc')->get(),
            'editingMenu' => $request->filled('edit') ? Menu::findOrFail($request->integer('edit')) : null,
        ]);
    }

    private function ensureMenuFiles(string $slug, string $name): void
    {
        $viewPath = resource_path("views/menus/{$slug}.blade.php");
        $cssDir = public_path('css/menus');
        $jsDir = public_path('js/menus');

        if (!File::exists($viewPath)) {
            File::ensureDirectoryExists(dirname($viewPath));
            File::put($viewPath, <<<BLADE
@extends('layouts.app')

@section('title', '{$name} | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ asset('css/menus/{$slug}.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> <b>{$name}</b></div>
            <h1 class="page-heading">{$name}</h1>
            <p class="page-intro">This workspace page is ready for its module content.</p>
            <section class="blank-card" aria-label="{$name} workspace"></section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/menus/{$slug}.js') }}" defer></script>
@endpush
BLADE
            );
        }

        File::ensureDirectoryExists($cssDir);
        File::ensureDirectoryExists($jsDir);

        $cssPath = "{$cssDir}/{$slug}.css";
        $jsPath = "{$jsDir}/{$slug}.js";

        if (!File::exists($cssPath)) {
            File::put($cssPath, "/* Styles for {$name} menu page */\n");
        }

        if (!File::exists($jsPath)) {
            File::put($jsPath, "// Scripts for {$name} menu page\n");
        }
    }

    public function store(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('menu-create', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'menu_type' => ['required', 'in:parent,child'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
        ]);

        if ($validated['menu_type'] === 'child' && empty($validated['parent_id'])) {
            return back()->withInput()->withErrors([
                'parent_id' => 'Please select a parent menu for this child menu.',
            ]);
        }

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $suffix = 2;

        while (Menu::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        Menu::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['menu_type'] === 'child' ? $validated['parent_id'] : null,
            'is_active' => true,
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $this->ensureMenuFiles($slug, $validated['name']);

        return redirect()->route('menu.create')->with('success', 'Menu created successfully.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('menu-create', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'menu_type' => ['required', 'in:parent,child'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
        ]);

        if ($validated['menu_type'] === 'child' && empty($validated['parent_id'])) {
            return back()->withInput()->withErrors([
                'parent_id' => 'Please select a parent menu for this child menu.',
            ]);
        }

        $menu->update([
            'name' => $validated['name'],
            'parent_id' => $validated['menu_type'] === 'child' ? $validated['parent_id'] : null,
            'updated_by' => session('authenticated_email') ?? null,
        ]);

        return redirect()->route('menu.create')->with('success', 'Menu updated successfully.');
    }

    public function toggleStatus(Request $request, Menu $menu): \Illuminate\Http\JsonResponse
    {
        if (!\App\Helpers\PermissionHelper::check('menu-create', 'update')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $menu->is_active = !$menu->is_active;
        $menu->updated_by = session('authenticated_email') ?? null;
        $menu->save();

        return response()->json(['success' => true, 'is_active' => $menu->is_active]);
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('menu-create', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        $menu->delete();
        return redirect()->route('menu.create')->with('success', 'Menu deleted.');
    }
}
