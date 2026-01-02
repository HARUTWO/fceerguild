<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DegreeType;

class DegreeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $degreeTypes = [
            ['name' => 'Bachelor of Science', 'abbreviation' => 'BS'],
            ['name' => 'Bachelor of Arts', 'abbreviation' => 'BA'],
            ['name' => 'Master of Science', 'abbreviation' => 'MS'],
            ['name' => 'Master of Arts', 'abbreviation' => 'MA'],
            ['name' => 'Master of Business Administration', 'abbreviation' => 'MBA'],
            ['name' => 'Doctor of Philosophy', 'abbreviation' => 'PhD'],
            ['name' => 'Doctor of Medicine', 'abbreviation' => 'MD'],
            ['name' => 'Juris Doctor', 'abbreviation' => 'JD'],
            ['name' => 'Bachelor of Laws', 'abbreviation' => 'LLB'],
            ['name' => 'Master of Engineering', 'abbreviation' => 'MEng'],
        ];

        foreach ($degreeTypes as $type) {
            DegreeType::firstOrCreate(
                ['name' => $type['name']],
                ['abbreviation' => $type['abbreviation']]
            );
        }
    }
}
