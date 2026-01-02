<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            ['name' => 'University of the Philippines', 'abbreviation' => 'UP'],
            ['name' => 'Ateneo de Manila University', 'abbreviation' => 'ADMU'],
            ['name' => 'De La Salle University', 'abbreviation' => 'DLSU'],
            ['name' => 'University of Santo Tomas', 'abbreviation' => 'UST'],
            ['name' => 'Mapua University', 'abbreviation' => 'Mapua'],
            ['name' => 'Polytechnic University of the Philippines', 'abbreviation' => 'PUP'],
            ['name' => 'Technological University of the Philippines', 'abbreviation' => 'TUP'],
            ['name' => 'Far Eastern University', 'abbreviation' => 'FEU'],
            ['name' => 'Adamson University', 'abbreviation' => 'AdU'],
            ['name' => 'National University', 'abbreviation' => 'NU'],
        ];

        foreach ($universities as $university) {
            University::firstOrCreate(
                ['name' => $university['name']],
                ['abbreviation' => $university['abbreviation']]
            );
        }
    }
}
