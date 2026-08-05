<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use App\Models\StateType;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::first();
        $stateType = StateType::first();

        if (! $country || ! $stateType) {
            return;
        }

        State::firstOrCreate(
            ['slug' => 'example-state'],
            [
                'country_id' => $country->id,
                'state_type' => $stateType->name,
                'name' => 'Example State',
                'slug' => 'example-state',
                'image_path' => null,
                'our_operation' => true,
                'is_capital' => true,
                'lat_log_name' => 'Example Capital',
                'address' => '456 State Avenue',
                'latitude' => '11.0000',
                'longitude' => '21.0000',
                'created_by' => 'system',
                'updated_by' => 'system',
            ]
        );
    }
}
