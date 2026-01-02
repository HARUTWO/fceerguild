<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Province;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $province = Province::first();

        if (!$province) {
            $province = Province::create(['name' => 'Metro Manila']);
        }

        $cities = [
            'Manila',
            'Quezon City',
            'Makati',
            'Pasig',
            'Taguig',
            'Parañaque',
            'Pasay',
            'Mandaluyong',
            'San Juan',
            'Caloocan',
        ];

        foreach ($cities as $name) {
            City::firstOrCreate(
                ['name' => $name],
                ['province_id' => $province->id]
            );
        }
    }
}
