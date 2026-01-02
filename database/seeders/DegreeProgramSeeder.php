<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DegreeProgram;
use App\Models\DegreeLevel;
use App\Models\DegreeType;
use App\Models\DegreeField;

class DegreeProgramSeeder extends Seeder
{
    public function run(): void
    {
        $degreeLevel = DegreeLevel::first() ?? DegreeLevel::create(['name' => 'Bachelor', 'abbreviation' => 'BS']);
        $degreeType = DegreeType::first() ?? DegreeType::create(['name' => 'Bachelor of Science', 'abbreviation' => 'BS']);
        $degreeField = DegreeField::first() ?? DegreeField::create(['name' => 'Engineering', 'abbreviation' => 'Engr']);

        $degreePrograms = [
            ['name' => 'Bachelor of Science in Civil Engineering', 'abbreviation' => 'BSCE'],
            ['name' => 'Bachelor of Science in Electrical Engineering', 'abbreviation' => 'BSEE'],
            ['name' => 'Bachelor of Science in Mechanical Engineering', 'abbreviation' => 'BSME'],
            ['name' => 'Bachelor of Science in Computer Engineering', 'abbreviation' => 'BSCpE'],
            ['name' => 'Bachelor of Science in Electronics Engineering', 'abbreviation' => 'BSECE'],
            ['name' => 'Bachelor of Science in Chemical Engineering', 'abbreviation' => 'BSChE'],
            ['name' => 'Bachelor of Science in Industrial Engineering', 'abbreviation' => 'BSIE'],
            ['name' => 'Bachelor of Science in Computer Science', 'abbreviation' => 'BSCS'],
            ['name' => 'Bachelor of Science in Information Technology', 'abbreviation' => 'BSIT'],
            ['name' => 'Bachelor of Science in Architecture', 'abbreviation' => 'BSArch'],
        ];

        foreach ($degreePrograms as $program) {
            DegreeProgram::firstOrCreate(
                ['name' => $program['name']],
                [
                    'abbreviation' => $program['abbreviation'],
                    'degree_level_id' => $degreeLevel->id,
                    'degree_type_id' => $degreeType->id,
                    'degree_field_id' => $degreeField->id,
                ]
            );
        }
    }
}
