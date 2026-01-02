<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DegreeField;

class DegreeFieldSeeder extends Seeder
{
    public function run(): void
    {
        $degreeFields = [
            ['name' => 'Engineering', 'abbreviation' => 'Engr'],
            ['name' => 'Computer Science', 'abbreviation' => 'CS'],
            ['name' => 'Information Technology', 'abbreviation' => 'IT'],
            ['name' => 'Business Administration', 'abbreviation' => 'BA'],
            ['name' => 'Accountancy', 'abbreviation' => 'Acctg'],
            ['name' => 'Education', 'abbreviation' => 'Educ'],
            ['name' => 'Nursing', 'abbreviation' => 'Nurs'],
            ['name' => 'Architecture', 'abbreviation' => 'Arch'],
            ['name' => 'Mathematics', 'abbreviation' => 'Math'],
            ['name' => 'Physics', 'abbreviation' => 'Phys'],
        ];

        foreach ($degreeFields as $field) {
            DegreeField::firstOrCreate(
                ['name' => $field['name']],
                ['abbreviation' => $field['abbreviation']]
            );
        }
    }
}
