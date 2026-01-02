<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HighschoolSubject;

class HighschoolSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'Mathematics',
            'English',
            'Science',
            'Filipino',
            'Social Studies',
            'Physical Education',
            'Music',
            'Arts',
            'Health',
            'Technology and Livelihood Education',
        ];

        foreach ($subjects as $name) {
            HighschoolSubject::firstOrCreate(['name' => $name]);
        }
    }
}
