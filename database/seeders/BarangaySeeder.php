<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use App\Models\City;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        $city = City::first();

        if (!$city) {
            $city = City::create([
                'name' => 'Manila',
                'province_id' => 1,
            ]);
        }

        $barangays = [
            'Barangay 1',
            'Barangay 2',
            'Barangay 3',
            'Barangay 4',
            'Barangay 5',
            'Barangay 6',
            'Barangay 7',
            'Barangay 8',
            'Barangay 9',
            'Barangay 10',
        ];

        foreach ($barangays as $name) {
            Barangay::firstOrCreate(
                ['name' => $name, 'city_id' => $city->id]
            );
        }
    }
}
