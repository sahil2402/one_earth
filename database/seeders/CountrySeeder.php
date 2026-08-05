<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::firstOrCreate(
            ['slug' => 'example-country'],
            [
                'name' => 'Example Country',
                'code' => 'EX',
                'address' => '123 Example Street',
                'latitude' => '10.0000',
                'longitude' => '20.0000',
                'summary' => 'An example country used for development seeding.',
                'description' => 'This country is seeded for sample data and local testing.',
                'iso_code' => 'EX',
                'phone_code' => '123',
                'isd_code' => '+123',
                'is_active' => true,
                'created_by' => 'system',
                'updated_by' => 'system',
            ]
        );
    }
}
