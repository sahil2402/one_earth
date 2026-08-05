<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $home = Menu::firstOrCreate([
            'slug' => 'home',
        ], [
            'name' => 'Home',
            'parent_id' => null,
            'is_active' => true,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);

        $about = Menu::firstOrCreate([
            'slug' => 'about',
        ], [
            'name' => 'About',
            'parent_id' => null,
            'is_active' => true,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);

        $services = Menu::firstOrCreate([
            'slug' => 'services',
        ], [
            'name' => 'Services',
            'parent_id' => null,
            'is_active' => true,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);

        Menu::firstOrCreate([
            'slug' => 'contact',
        ], [
            'name' => 'Contact',
            'parent_id' => $home->id,
            'is_active' => true,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);
    }
}
