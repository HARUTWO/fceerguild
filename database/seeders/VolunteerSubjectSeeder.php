<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VolunteerSubject;

class VolunteerSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'MATH101', 'name' => 'Basic Mathematics', 'description' => 'Fundamentals of mathematics'],
            ['code' => 'MATH201', 'name' => 'Advanced Mathematics', 'description' => 'Advanced mathematical concepts'],
            ['code' => 'PHYS101', 'name' => 'General Physics', 'description' => 'Introduction to physics'],
            ['code' => 'CHEM101', 'name' => 'General Chemistry', 'description' => 'Introduction to chemistry'],
            ['code' => 'ENG101', 'name' => 'English Communication', 'description' => 'English language and communication'],
            ['code' => 'GEAS101', 'name' => 'General Engineering and Applied Sciences', 'description' => 'Engineering fundamentals'],
            ['code' => 'EST101', 'name' => 'Engineering Sciences and Technology', 'description' => 'Applied engineering sciences'],
            ['code' => 'CALC101', 'name' => 'Calculus', 'description' => 'Differential and integral calculus'],
            ['code' => 'STAT101', 'name' => 'Statistics', 'description' => 'Probability and statistics'],
            ['code' => 'LOGIC101', 'name' => 'Logic and Critical Thinking', 'description' => 'Logical reasoning and analysis'],
        ];

        foreach ($subjects as $subject) {
            VolunteerSubject::firstOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'description' => $subject['description'],
                ]
            );
        }
    }
}
