<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::first();
        $state = State::first();

        if (! $country || ! $state) {
            return;
        }

        City::firstOrCreate(
            ['slug' => 'example-city'],
            [
                'country_id' => $country->id,
                'state_id' => $state->id,
                'name' => 'Example City',
                'slug' => 'example-city',
                'time_to_visit' => '3 days',
                'currency' => 'EXD',
                'language' => 'Examplean',
                'introduction' => 'A sample city for seed data.',
                'lat_log_name' => 'Example City Center',
                'address' => '789 City Road',
                'latitude' => '12.0000',
                'longitude' => '22.0000',
                'description' => 'Example City is seeded to verify city relationships.',
                'seo_title' => 'Example City',
                'meta_keyword' => 'example,city,seed',
                'meta_description' => 'Seed city record for application testing.',
                'banner_image' => null,
                'thumb_image' => null,
                'our_operation' => true,
                'is_capital' => false,
                'created_by' => 'system',
                'updated_by' => 'system',
            ]
        );
    }
}
