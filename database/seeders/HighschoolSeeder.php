<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Highschool;

class HighschoolSeeder extends Seeder
{
    public function run(): void
    {
        $highschools = [
            ['name' => 'Philippine Science High School', 'abbreviation' => 'PSHS'],
            ['name' => 'Manila Science High School', 'abbreviation' => 'MSHS'],
            ['name' => 'Quezon City Science High School', 'abbreviation' => 'QCSHS'],
            ['name' => 'Makati Science High School', 'abbreviation' => 'MakSHS'],
            ['name' => 'Pasig City Science High School', 'abbreviation' => 'PCSHS'],
            ['name' => 'Marikina Science High School', 'abbreviation' => 'MarSHS'],
            ['name' => 'Taguig Science High School', 'abbreviation' => 'TSHS'],
            ['name' => 'Valenzuela City Science High School', 'abbreviation' => 'VCSHS'],
            ['name' => 'Caloocan Science High School', 'abbreviation' => 'CSHS'],
            ['name' => 'Las Piñas Science High School', 'abbreviation' => 'LPSHS'],
        ];

        foreach ($highschools as $highschool) {
            Highschool::firstOrCreate(
                ['name' => $highschool['name']],
                ['abbreviation' => $highschool['abbreviation']]
            );
        }
    }
}
