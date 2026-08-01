<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuPageController;
use App\Http\Controllers\Settings\MenuController;
use App\Http\Controllers\Settings\RoleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/debug-paths', function() {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    $dir = storage_path('framework/views');
    $files = is_dir($dir) ? scandir($dir) : [];
    return [
        'views_path' => $dir,
        'files' => $files,
    ];
});

Route::controller(LoginController::class)->group(function (): void {
    Route::get('/login', 'create')->name('login');
    Route::post('/login', 'store')->name('login.store');
    Route::post('/logout', 'destroy')->name('logout');
});

Route::middleware('role.auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/settings/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/settings/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
    Route::patch('/settings/menu/{menu}/status', [MenuController::class, 'toggleStatus'])->name('menu.toggleStatus');
    Route::delete('/settings/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::get('/menus/{menu:slug}/create', [MenuPageController::class, 'createItem'])->name('menus.create');
    Route::get('/settings/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/settings/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/settings/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::patch('/settings/roles/{role}/status', [RoleController::class, 'toggleStatus'])->name('roles.toggleStatus');
    Route::delete('/settings/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/menus/{menu:slug}', [MenuPageController::class, 'show'])->name('menus.show');
    Route::post('/menus/users', [MenuPageController::class, 'storeUser'])->name('menus.users.store');
    Route::put('/menus/users/{user}', [MenuPageController::class, 'updateUser'])->name('menus.users.update');
    Route::delete('/menus/users/{user}', [MenuPageController::class, 'destroyUser'])->name('menus.users.destroy');
    Route::patch('/menus/users/{user}/status', [MenuPageController::class, 'toggleUserStatus'])->name('menus.users.toggleStatus');

    Route::post('/menus/state', [MenuPageController::class, 'storeState'])->name('menus.states.store');
    Route::put('/menus/state/{state}', [MenuPageController::class, 'updateState'])->name('menus.states.update');
    Route::delete('/menus/state/{state}', [MenuPageController::class, 'destroyState'])->name('menus.states.destroy');

    Route::post('/menus/state-type', [MenuPageController::class, 'storeStateType'])->name('menus.state-types.store');
    Route::put('/menus/state-type/{state_type}', [MenuPageController::class, 'updateStateType'])->name('menus.state-types.update');
    Route::delete('/menus/state-type/{state_type}', [MenuPageController::class, 'destroyStateType'])->name('menus.state-types.destroy');

    Route::post('/menus/country', [MenuPageController::class, 'storeCountry'])->name('menus.countries.store');
    Route::put('/menus/country/{country}', [MenuPageController::class, 'updateCountry'])->name('menus.countries.update');
    Route::delete('/menus/country/{country}', [MenuPageController::class, 'destroyCountry'])->name('menus.countries.destroy');

    Route::post('/menus/city', [MenuPageController::class, 'storeCity'])->name('menus.cities.store');
    Route::put('/menus/city/{city}', [MenuPageController::class, 'updateCity'])->name('menus.cities.update');
    Route::delete('/menus/city/{city}', [MenuPageController::class, 'destroyCity'])->name('menus.cities.destroy');
    // Website settings (owner only)
    Route::get('/settings/website', [\App\Http\Controllers\Settings\WebsiteSettingController::class, 'edit'])->name('settings.website');
    Route::post('/settings/website', [\App\Http\Controllers\Settings\WebsiteSettingController::class, 'update'])->name('settings.website.update');
});
