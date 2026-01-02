<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldOfWork;

class FieldOfWorkSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['name' => 'Attorney', 'description' => 'Legal professional - Registered Attorney with law degree and bar exam certification'],
            ['name' => 'Engineer', 'description' => 'Engineering professional - Registered Engineer with engineering degree and board exam certification'],
            ['name' => 'Architect', 'description' => 'Architecture professional - Licensed Architect with architecture degree and board exam certification'],
            ['name' => 'Accountant', 'description' => 'Finance professional - Certified Public Accountant with accounting degree and CPA certification'],
            ['name' => 'Librarian', 'description' => 'Information professional - Registered Librarian with library science degree and professional certification'],
            ['name' => 'Physician', 'description' => 'Medical professional - Licensed Physician with medical degree and board exam certification'],
            ['name' => 'Nurse', 'description' => 'Healthcare professional - Registered Nurse with nursing degree and board exam certification'],
            ['name' => 'Teacher', 'description' => 'Education professional - Licensed Teacher with education degree and teaching certification'],
            ['name' => 'Psychologist', 'description' => 'Mental health professional - Licensed Psychologist with psychology degree and professional certification'],
            ['name' => 'Social Worker', 'description' => 'Social service professional - Licensed Social Worker with social work degree and professional certification'],
        ];

        foreach ($fields as $field) {
            FieldOfWork::firstOrCreate(
                ['name' => $field['name']],
                ['description' => $field['description']]
            );
        }
    }
}
