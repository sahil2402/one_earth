<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Seeder;

class RoleMenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Admin')->first();
        $menus = Menu::all();

        if (! $role || $menus->isEmpty()) {
            return;
        }

        foreach ($menus as $menu) {
            RoleMenuPermission::firstOrCreate([
                'role_id' => $role->id,
                'menu_id' => $menu->id,
            ], [
                'can_view' => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => true,
            ]);
        }
    }
}
