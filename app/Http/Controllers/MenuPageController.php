<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MenuPageController extends Controller
{
    /**
     * Render the workspace page assigned to a database-backed navigation menu.
     */
    public function show(Menu $menu): View|RedirectResponse
    {
        // Run migrations programmatically
        \Illuminate\Support\Facades\Artisan::call('migrate');

        if (in_array($menu->slug, ['roleandpermission', 'role-and-permission'], true)) {
            return redirect()->route('roles.index');
        }

        if (!\App\Helpers\PermissionHelper::check($menu->slug, 'view')) {
            abort(403, 'Unauthorized.');
        }

        $menuView = "menus.{$menu->slug}";
        if (!view()->exists($menuView)) {
            $this->createMenuView($menu->slug, $menu->name);
        }

        $data = [
            'menu' => $menu,
            'role' => session('authenticated_role'),
            'email' => session('authenticated_email'),
            'navbarTitle' => $menu->name,
        ];

        if ($menu->slug === 'users') {
            $data['users'] = User::with('role')->get();
        } elseif ($menu->slug === 'state') {
            $data['states'] = \App\Models\State::with('country')->get();
        } elseif ($menu->slug === 'country') {
            $data['countries'] = \App\Models\Country::all();
        } elseif ($menu->slug === 'city') {
            $data['cities'] = \App\Models\City::with(['country', 'state'])->get();
        } elseif ($menu->slug === 'state-type') {
            $data['state_types'] = \App\Models\StateType::all();
        }

        return view($menuView, $data);
    }

    public function createItem(Request $request, Menu $menu): View|RedirectResponse
    {
        if (in_array($menu->slug, ['roleandpermission', 'role-and-permission'], true)) {
            return redirect()->route('roles.index');
        }

        if (!\App\Helpers\PermissionHelper::check($menu->slug, 'create')) {
            abort(403, 'Unauthorized.');
        }

        $createView = "menus.{$menu->slug}-create";
        if (!view()->exists($createView)) {
            $this->createMenuCreateView($menu->slug, $menu->name);
        }

        $data = [
            'menu' => $menu,
            'role' => session('authenticated_role'),
            'email' => session('authenticated_email'),
            'navbarTitle' => $menu->name,
        ];

        if ($menu->slug === 'users') {
            $data['roles'] = Role::query()->where('is_active', true)->orderBy('name')->get();
            $data['designations'] = User::query()
                ->whereNotNull('designation')
                ->where('designation', '<>', '')
                ->distinct()
                ->pluck('designation');
            $data['editingUser'] = $request->filled('edit') ? User::findOrFail($request->integer('edit')) : null;
        } elseif ($menu->slug === 'state') {
            $data['countries'] = \App\Models\Country::where('is_active', true)->orderBy('name')->get();
            $data['state_types'] = \App\Models\StateType::all();
            $data['editingState'] = $request->filled('edit') ? \App\Models\State::findOrFail($request->integer('edit')) : null;
        } elseif ($menu->slug === 'country') {
            $data['editingCountry'] = $request->filled('edit') ? \App\Models\Country::findOrFail($request->integer('edit')) : null;
        } elseif ($menu->slug === 'city') {
            $data['countries'] = \App\Models\Country::where('is_active', true)->orderBy('name')->get();
            $data['states'] = \App\Models\State::orderBy('name')->get();
            $data['editingCity'] = $request->filled('edit') ? \App\Models\City::findOrFail($request->integer('edit')) : null;
        } elseif ($menu->slug === 'state-type') {
            $data['editingStateType'] = $request->filled('edit') ? \App\Models\StateType::findOrFail($request->integer('edit')) : null;
        }

        return view($createView, $data);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('users', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'designation' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'designation' => $validated['designation'] ?? null,
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $usersMenu = Menu::where('slug', 'users')->first();
        if ($request->has('return_here')) {
            return redirect()->route('menus.create', $usersMenu)->with('success', 'User created successfully.');
        }

        return redirect()->route('menus.show', $usersMenu)->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('users', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'designation' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'designation' => $validated['designation'] ?? null,
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
            'updated_by' => session('authenticated_email') ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($updateData);

        $usersMenu = Menu::where('slug', 'users')->first();
        return redirect()->route('menus.show', $usersMenu)->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('users', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        $user->delete();
        $usersMenu = Menu::where('slug', 'users')->first();
        return redirect()->route('menus.show', $usersMenu)->with('success', 'User deleted successfully.');
    }

    public function toggleUserStatus(Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        if (!\App\Helpers\PermissionHelper::check('users', 'update')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update([
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
        ]);
    }

    public function storeState(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:states,slug'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'our_operation' => ['nullable', 'boolean'],
            'is_capital' => ['nullable', 'boolean'],
            'lat_log_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/states'));
            $file->move(public_path('uploads/states'), $filename);
            $imagePath = 'uploads/states/' . $filename;
        }

        \App\Models\State::create([
            'country_id' => $validated['country_id'],
            'state_type' => $validated['state_type'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'image_path' => $imagePath,
            'our_operation' => $request->boolean('our_operation', false),
            'is_capital' => $request->boolean('is_capital', false),
            'lat_log_name' => $validated['lat_log_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $stateMenu = Menu::where('slug', 'state')->first();
        if ($request->has('return_here')) {
            return redirect()->route('menus.create', $stateMenu)->with('success', 'State created successfully.');
        }

        return redirect()->route('menus.show', $stateMenu)->with('success', 'State created successfully.');
    }

    public function updateState(Request $request, \App\Models\State $state): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:states,slug,' . $state->id],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'our_operation' => ['nullable', 'boolean'],
            'is_capital' => ['nullable', 'boolean'],
            'lat_log_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
        ]);

        $imagePath = $state->image_path;
        if ($request->hasFile('image_path')) {
            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $file = $request->file('image_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/states'));
            $file->move(public_path('uploads/states'), $filename);
            $imagePath = 'uploads/states/' . $filename;
        }

        $state->update([
            'country_id' => $validated['country_id'],
            'state_type' => $validated['state_type'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'image_path' => $imagePath,
            'our_operation' => $request->boolean('our_operation', false),
            'is_capital' => $request->boolean('is_capital', false),
            'lat_log_name' => $validated['lat_log_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'updated_by' => session('authenticated_email') ?? null,
        ]);

        $stateMenu = Menu::where('slug', 'state')->first();
        return redirect()->route('menus.show', $stateMenu)->with('success', 'State updated successfully.');
    }

    public function destroyState(\App\Models\State $state): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        if ($state->image_path && file_exists(public_path($state->image_path))) {
            @unlink(public_path($state->image_path));
        }

        $state->delete();
        $stateMenu = Menu::where('slug', 'state')->first();
        return redirect()->route('menus.show', $stateMenu)->with('success', 'State deleted successfully.');
    }

    public function storeCountry(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('country', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:countries,slug'],
            'country_code' => ['required', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'iso_code' => ['nullable', 'string', 'max:10'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'isd_code' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/countries'));
            $file->move(public_path('uploads/countries'), $filename);
            $bannerPath = 'uploads/countries/' . $filename;
        }

        \App\Models\Country::create([
            'name' => $validated['country_name'],
            'slug' => $validated['slug'],
            'code' => $validated['country_code'],
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'banner_image' => $bannerPath,
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'iso_code' => $validated['iso_code'] ?? null,
            'phone_code' => $validated['phone_code'] ?? null,
            'isd_code' => $validated['isd_code'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $countryMenu = Menu::where('slug', 'country')->first();
        if ($request->has('return_here')) {
            return redirect()->route('menus.create', $countryMenu)->with('success', 'Country created successfully.');
        }

        return redirect()->route('menus.show', $countryMenu)->with('success', 'Country created successfully.');
    }

    public function updateCountry(Request $request, \App\Models\Country $country): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('country', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:countries,slug,' . $country->id],
            'country_code' => ['required', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'iso_code' => ['nullable', 'string', 'max:10'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'isd_code' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bannerPath = $country->banner_image;
        if ($request->hasFile('banner_image')) {
            if ($bannerPath && file_exists(public_path($bannerPath))) {
                @unlink(public_path($bannerPath));
            }
            $file = $request->file('banner_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/countries'));
            $file->move(public_path('uploads/countries'), $filename);
            $bannerPath = 'uploads/countries/' . $filename;
        }

        $country->update([
            'name' => $validated['country_name'],
            'slug' => $validated['slug'],
            'code' => $validated['country_code'],
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'banner_image' => $bannerPath,
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'iso_code' => $validated['iso_code'] ?? null,
            'phone_code' => $validated['phone_code'] ?? null,
            'isd_code' => $validated['isd_code'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'updated_by' => session('authenticated_email') ?? null,
        ]);

        $countryMenu = Menu::where('slug', 'country')->first();
        return redirect()->route('menus.show', $countryMenu)->with('success', 'Country updated successfully.');
    }

    public function destroyCountry(\App\Models\Country $country): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('country', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        if ($country->banner_image && file_exists(public_path($country->banner_image))) {
            @unlink(public_path($country->banner_image));
        }

        $country->delete();
        $countryMenu = Menu::where('slug', 'country')->first();
        return redirect()->route('menus.show', $countryMenu)->with('success', 'Country deleted successfully.');
    }

    public function storeCity(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('city', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:cities,slug'],
            'time_to_visit' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string'],
            'lat_log_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_keyword' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'thumb_image' => ['nullable', 'image', 'max:2048'],
            'our_operation' => ['nullable', 'boolean'],
            'is_capital' => ['nullable', 'boolean'],
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time() . '_banner_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/cities'));
            $file->move(public_path('uploads/cities'), $filename);
            $bannerPath = 'uploads/cities/' . $filename;
        }

        $thumbPath = null;
        if ($request->hasFile('thumb_image')) {
            $file = $request->file('thumb_image');
            $filename = time() . '_thumb_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/cities'));
            $file->move(public_path('uploads/cities'), $filename);
            $thumbPath = 'uploads/cities/' . $filename;
        }

        \App\Models\City::create([
            'country_id' => $validated['country_id'],
            'state_id' => $validated['state_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'time_to_visit' => $validated['time_to_visit'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'language' => $validated['language'] ?? null,
            'introduction' => $validated['introduction'] ?? null,
            'lat_log_name' => $validated['lat_log_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_keyword' => $validated['meta_keyword'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'banner_image' => $bannerPath,
            'thumb_image' => $thumbPath,
            'our_operation' => $request->boolean('our_operation', false),
            'is_capital' => $request->boolean('is_capital', false),
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $cityMenu = Menu::where('slug', 'city')->first();
        if ($request->has('return_here')) {
            return redirect()->route('menus.create', $cityMenu)->with('success', 'City created successfully.');
        }

        return redirect()->route('menus.show', $cityMenu)->with('success', 'City created successfully.');
    }

    public function updateCity(Request $request, \App\Models\City $city): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('city', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:cities,slug,' . $city->id],
            'time_to_visit' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string'],
            'lat_log_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_keyword' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'thumb_image' => ['nullable', 'image', 'max:2048'],
            'our_operation' => ['nullable', 'boolean'],
            'is_capital' => ['nullable', 'boolean'],
        ]);

        $bannerPath = $city->banner_image;
        if ($request->hasFile('banner_image')) {
            if ($bannerPath && file_exists(public_path($bannerPath))) {
                @unlink(public_path($bannerPath));
            }
            $file = $request->file('banner_image');
            $filename = time() . '_banner_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/cities'));
            $file->move(public_path('uploads/cities'), $filename);
            $bannerPath = 'uploads/cities/' . $filename;
        }

        $thumbPath = $city->thumb_image;
        if ($request->hasFile('thumb_image')) {
            if ($thumbPath && file_exists(public_path($thumbPath))) {
                @unlink(public_path($thumbPath));
            }
            $file = $request->file('thumb_image');
            $filename = time() . '_thumb_' . $file->getClientOriginalName();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/cities'));
            $file->move(public_path('uploads/cities'), $filename);
            $thumbPath = 'uploads/cities/' . $filename;
        }

        $city->update([
            'country_id' => $validated['country_id'],
            'state_id' => $validated['state_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'time_to_visit' => $validated['time_to_visit'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'language' => $validated['language'] ?? null,
            'introduction' => $validated['introduction'] ?? null,
            'lat_log_name' => $validated['lat_log_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_keyword' => $validated['meta_keyword'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'banner_image' => $bannerPath,
            'thumb_image' => $thumbPath,
            'our_operation' => $request->boolean('our_operation', false),
            'is_capital' => $request->boolean('is_capital', false),
            'updated_by' => session('authenticated_email') ?? null,
        ]);

        $cityMenu = Menu::where('slug', 'city')->first();
        return redirect()->route('menus.show', $cityMenu)->with('success', 'City updated successfully.');
    }

    public function destroyCity(\App\Models\City $city): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('city', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        if ($city->banner_image && file_exists(public_path($city->banner_image))) {
            @unlink(public_path($city->banner_image));
        }

        if ($city->thumb_image && file_exists(public_path($city->thumb_image))) {
            @unlink(public_path($city->thumb_image));
        }

        $city->delete();
        $cityMenu = Menu::where('slug', 'city')->first();
        return redirect()->route('menus.show', $cityMenu)->with('success', 'City deleted successfully.');
    }

    public function storeStateType(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state-type', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:state_types,name'],
        ]);

        \App\Models\StateType::create([
            'name' => $validated['name'],
            'created_by' => session('authenticated_email') ?? null,
        ]);

        $menu = Menu::where('slug', 'state-type')->first();
        if ($request->has('return_here')) {
            return redirect()->route('menus.create', $menu)->with('success', 'State Type created successfully.');
        }

        return redirect()->route('menus.show', $menu)->with('success', 'State Type created successfully.');
    }

    public function updateStateType(Request $request, \App\Models\StateType $state_type): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state-type', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:state_types,name,' . $state_type->id],
        ]);

        $state_type->update([
            'name' => $validated['name'],
            'updated_by' => session('authenticated_email') ?? null,
        ]);

        $menu = Menu::where('slug', 'state-type')->first();
        return redirect()->route('menus.show', $menu)->with('success', 'State Type updated successfully.');
    }

    public function destroyStateType(\App\Models\StateType $state_type): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('state-type', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        $state_type->delete();
        $menu = Menu::where('slug', 'state-type')->first();
        return redirect()->route('menus.show', $menu)->with('success', 'State Type deleted successfully.');
    }

    private function createMenuView(string $slug, string $name): void
    {
        $viewPath = resource_path("views/menus/{$slug}.blade.php");
        $cssDir = public_path('css/menus');
        $jsDir = public_path('js/menus');

        File::ensureDirectoryExists(dirname($viewPath));
        File::ensureDirectoryExists($cssDir);
        File::ensureDirectoryExists($jsDir);

        if (!File::exists($viewPath)) {
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

            <section class="card roles-table-card">
                <div class="card-header">
                    <div>
                        <h2>{$name} manager</h2>
                        <p>View and manage country records for this workspace.</p>
                    </div>
                    <div class="table-action-row">
                        @include('components.datatable', ['tableId' => 'menu-table'])
                    </div>
                </div>

                <div class="table-card">
                    <table id="menu-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Country Name</th>
                                <th>Country Type</th>
                                <th>ISO Code</th>
                                <th>ISD Code</th>
                                <th>Is Active</th>
                                <th>Image Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="empty">No records found. Use the module actions to add new entries.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
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

        $cssPath = "{$cssDir}/{$slug}.css";
        $jsPath = "{$jsDir}/{$slug}.js";

        if (!File::exists($cssPath)) {
            File::put($cssPath, "/* Styles for {$name} menu page */\n");
        }

        if (!File::exists($jsPath)) {
            File::put($jsPath, <<<JS
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.datatable-toolbar').forEach(function (toolbar) {
        var tableId = toolbar.dataset.tableId;
        var table = document.getElementById(tableId);
        var searchInput = document.getElementById(tableId + '-search');

        if (!table || !searchInput) {
            return;
        }

        var rows = Array.from(table.tBodies[0]?.rows || []);

        searchInput.addEventListener('input', function () {
            var query = this.value.trim().toLowerCase();

            rows.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    });
});
JS
            );
        }
    }

    private function createMenuCreateView(string $slug, string $name): void
    {
        $viewPath = resource_path("views/menus/{$slug}-create.blade.php");
        $cssDir = public_path('css/menus');
        $jsDir = public_path('js/menus');

        File::ensureDirectoryExists(dirname($viewPath));
        File::ensureDirectoryExists($cssDir);
        File::ensureDirectoryExists($jsDir);

        if (!File::exists($viewPath)) {
            File::put($viewPath, <<<BLADE
@extends('layouts.app')

@section('title', 'Create {$name} | Travel Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/roles.css') }}">
<link rel="stylesheet" href="{{ asset('css/menus/{$slug}-create.css') }}">
@endpush

@section('content')
<div class="app-shell">
    @include('components.dashboard.sidebar')
    <div class="app-main">
        @include('components.dashboard.navbar')
        <main class="page-content">
            <div class="breadcrumb">Settings <span>›</span> <b>{$name}</b></div>
            <h1 class="page-heading">Create {$name}</h1>
            <p class="page-intro">Fill in the details below to add a new {$name} record.</p>

            <section class="card role-form-card">
                <div class="card-header">
                    <div>
                        <h2>Add new {$name}</h2>
                        <p>Create a new {$name} entry for the workspace.</p>
                    </div>
                </div>

                <form class="role-form-inner" method="POST" action="#">
                    @csrf
                    <div class="form-row">
                        <div class="field">
                            <label for="country_name">Country Name</label>
                            <input id="country_name" name="country_name" placeholder="Country Name" required>
                        </div>
                        <div class="field">
                            <label for="slug">Slug</label>
                            <input id="slug" name="slug" placeholder="Slug" required>
                        </div>
                        <div class="field">
                            <label for="country_code">Country Code (3 Digits)</label>
                            <input id="country_code" name="country_code" placeholder="Country Code (3 digits)" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="iso_code">ISO Code (2 Digits)</label>
                            <input id="iso_code" name="iso_code" placeholder="ISO Code (2 digits)" required>
                        </div>
                        <div class="field">
                            <label for="phone_code">Phone Code (2 Digits)</label>
                            <input id="phone_code" name="phone_code" placeholder="Phone Code (2 digits)" required>
                        </div>
                        <div class="field">
                            <label for="isd_code">Country ISD Code (With +)</label>
                            <input id="isd_code" name="isd_code" placeholder="+123" required>
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

                    <div class="form-row">
                        <div class="field">
                            <label for="tax_name">Tax Name</label>
                            <input id="tax_name" name="tax_name" placeholder="Tax Name">
                        </div>
                        <div class="field">
                            <label for="tax_percentage">Tax Percentage</label>
                            <input id="tax_percentage" name="tax_percentage" placeholder="Tax Percentage">
                        </div>
                        <div class="field" style="display:flex;align-items:center;gap:10px;margin-top:24px;">
                            <label class="status-option" style="margin:0;">
                                <input id="is_destination" name="is_destination" type="checkbox">
                                <span>Is Destination</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions form-actions-end">
                        <a class="cancel" href="{{ route('menus.show', \$menu) }}">Cancel</a>
                        <button class="save" type="submit">Save {$name}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/menus/{$slug}-create.js') }}" defer></script>
@endpush
BLADE
            );
        }

        $createCssPath = "{$cssDir}/{$slug}-create.css";
        $createJsPath = "{$jsDir}/{$slug}-create.js";

        if (!File::exists($createCssPath)) {
            File::put($createCssPath, "/* Styles for Create {$name} page */\n");
        }

        if (!File::exists($createJsPath)) {
            File::put($createJsPath, "// Scripts for Create {$name} page\n");
        }
    }
}
