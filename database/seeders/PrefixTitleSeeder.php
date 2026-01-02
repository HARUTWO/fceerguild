<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrefixTitle;
use App\Models\FieldOfWork;

class PrefixTitleSeeder extends Seeder
{
    public function run(): void
    {
        $fieldOfWork = FieldOfWork::first();

        $prefixTitles = [
            ['name' => 'Engineer', 'abbreviation' => 'Engr.'],
            ['name' => 'Doctor', 'abbreviation' => 'Dr.'],
            ['name' => 'Attorney', 'abbreviation' => 'Atty.'],
            ['name' => 'Professor', 'abbreviation' => 'Prof.'],
            ['name' => 'Architect', 'abbreviation' => 'Ar.'],
            ['name' => 'Mister', 'abbreviation' => 'Mr.'],
            ['name' => 'Miss', 'abbreviation' => 'Ms.'],
            ['name' => 'Missus', 'abbreviation' => 'Mrs.'],
            ['name' => 'Reverend', 'abbreviation' => 'Rev.'],
            ['name' => 'Honorable', 'abbreviation' => 'Hon.'],
        ];

        foreach ($prefixTitles as $title) {
            PrefixTitle::firstOrCreate(
                ['name' => $title['name']],
                [
                    'abbreviation' => $title['abbreviation'],
                    'field_of_work_id' => $fieldOfWork?->id,
                ]
            );
        }
    }
}
