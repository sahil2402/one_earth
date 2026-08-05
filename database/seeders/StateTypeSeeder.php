<?php

namespace Database\Seeders;

use App\Models\StateType;
use Illuminate\Database\Seeder;

class StateTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['State', 'Province', 'Territory'];

        foreach ($types as $type) {
            StateType::firstOrCreate([
                'name' => $type,
            ], [
                'created_by' => 'system',
                'updated_by' => 'system',
            ]);
        }
    }
}
