<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuffixTitle;
use App\Models\FieldOfWork;

class SuffixTitleSeeder extends Seeder
{
    public function run(): void
    {
        $fieldOfWork = FieldOfWork::first();

        $suffixTitles = [
            ['name' => 'Junior', 'abbreviation' => 'Jr.'],
            ['name' => 'Senior', 'abbreviation' => 'Sr.'],
            ['name' => 'The Third', 'abbreviation' => 'III'],
            ['name' => 'The Fourth', 'abbreviation' => 'IV'],
            ['name' => 'Doctor of Philosophy', 'abbreviation' => 'Ph.D.'],
            ['name' => 'Doctor of Medicine', 'abbreviation' => 'M.D.'],
            ['name' => 'Registered Engineer', 'abbreviation' => 'RE'],
            ['name' => 'Certified Public Accountant', 'abbreviation' => 'CPA'],
            ['name' => 'Master of Business Administration', 'abbreviation' => 'MBA'],
            ['name' => 'Licensed Architect', 'abbreviation' => 'UAP'],
        ];

        foreach ($suffixTitles as $title) {
            SuffixTitle::firstOrCreate(
                ['name' => $title['name']],
                [
                    'abbreviation' => $title['abbreviation'],
                    'field_of_work_id' => $fieldOfWork?->id,
                ]
            );
        }
    }
}
