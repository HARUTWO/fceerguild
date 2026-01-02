<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Reference table seeders (order matters for foreign key dependencies)
        $this->call([
            UserSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            BarangaySeeder::class,
            DegreeFieldSeeder::class,
            DegreeLevelSeeder::class,
            DegreeTypeSeeder::class,
            DegreeProgramSeeder::class,
            UniversitySeeder::class,
            HighschoolSeeder::class,
            HighschoolSubjectSeeder::class,
            FieldOfWorkSeeder::class,
            PrefixTitleSeeder::class,
            SuffixTitleSeeder::class,
            VolunteerSubjectSeeder::class,
            ReviewSeasonSeeder::class,
        ]);
    }
}
