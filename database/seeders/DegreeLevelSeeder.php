<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DegreeLevel;

class DegreeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $degreeLevels = [
            ['name' => 'Associate', 'abbreviation' => 'Assoc'],
            ['name' => 'Bachelor', 'abbreviation' => 'BS'],
            ['name' => 'Master', 'abbreviation' => 'MS'],
            ['name' => 'Doctorate', 'abbreviation' => 'PhD'],
            ['name' => 'Post-Doctorate', 'abbreviation' => 'PostDoc'],
            ['name' => 'Diploma', 'abbreviation' => 'Dip'],
            ['name' => 'Certificate', 'abbreviation' => 'Cert'],
            ['name' => 'Professional', 'abbreviation' => 'Prof'],
            ['name' => 'Specialist', 'abbreviation' => 'Spec'],
            ['name' => 'Graduate Certificate', 'abbreviation' => 'GradCert'],
        ];

        foreach ($degreeLevels as $level) {
            DegreeLevel::firstOrCreate(
                ['name' => $level['name']],
                ['abbreviation' => $level['abbreviation']]
            );
        }
    }
}
