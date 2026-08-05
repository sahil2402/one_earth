<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        WebsiteSetting::firstOrCreate(
            ['site_name' => 'Example Site'],
            [
                'logo_path' => null,
            ]
        );
    }
}
