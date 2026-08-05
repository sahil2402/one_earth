<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Editor', 'Viewer'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
            ], [
                'is_active' => true,
                'created_by' => 'system',
                'updated_by' => 'system',
            ]);
        }
    }
}
