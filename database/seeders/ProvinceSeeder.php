<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Metro Manila',
            'Cavite',
            'Laguna',
            'Batangas',
            'Bulacan',
            'Rizal',
            'Pampanga',
            'Cebu',
            'Davao del Sur',
            'Iloilo',
        ];

        foreach ($provinces as $name) {
            Province::firstOrCreate(['name' => $name]);
        }
    }
}
