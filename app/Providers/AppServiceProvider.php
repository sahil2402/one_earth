<?php

namespace App\Providers {

    use App\Models\Menu;
    use App\Models\WebsiteSetting;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\View;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            //
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            try {
                $settingsMenu = Menu::firstOrCreate(
                    ['slug' => 'settings'],
                    ['name' => 'Settings', 'parent_id' => null]
                );

                Menu::firstOrCreate(
                    ['slug' => 'menu-create'],
                    ['name' => 'Menu Create', 'parent_id' => $settingsMenu->id]
                );

                if (\App\Models\Country::count() === 0) {
                    \App\Models\Country::create([
                        'name' => 'India',
                        'slug' => 'india',
                        'code' => 'IND',
                        'iso_code' => 'IN',
                        'phone_code' => '91',
                        'isd_code' => '+91',
                    ]);
                    \App\Models\Country::create([
                        'name' => 'United States',
                        'slug' => 'united-states',
                        'code' => 'USA',
                        'iso_code' => 'US',
                        'phone_code' => '1',
                        'isd_code' => '+1',
                    ]);
                }

                if (\App\Models\StateType::count() === 0) {
                    \App\Models\StateType::create(['name' => 'State']);
                    \App\Models\StateType::create(['name' => 'Province']);
                    \App\Models\StateType::create(['name' => 'Union Territory']);
                }
            } catch (\Exception $e) {
                // Silence errors during migrations
            }

            try {
                $setting = WebsiteSetting::first();
                View::share('websiteSetting', $setting);
            } catch (\Exception $e) {
                // Silence errors during migrations
            }

            View::composer('components.dashboard.sidebar', function ($view): void {
                $navigation = Menu::query()
                    ->whereNull('parent_id')
                    ->with('children')
                    ->orderBy('name')
                    ->get();

                $navigation = \App\Helpers\PermissionHelper::filterMenus($navigation);

                $view->with('navigationMenus', $navigation);
            });
        }
    }
}

namespace {
    if (!function_exists('custom_asset')) {
        function custom_asset($path) {
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }
            $mode = config('constants.APP_ASSET_MODE', 'dev');
            if ($mode === 'live') {
                return asset('public/' . $path);
            }
            return asset($path);
        }
    }

    if (!function_exists('custom_upload')) {
        function custom_upload($path) {
            if (empty($path)) {
                return '';
            }
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }
            $mode = config('constants.APP_ASSET_MODE', 'dev');
            if ($mode === 'live') {
                return '/public/' . $path;
            }
            return '/' . $path;
        }
    }
}
