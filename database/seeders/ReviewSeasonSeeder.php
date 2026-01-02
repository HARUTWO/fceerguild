<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewSeason;
use App\Models\User;

class ReviewSeasonSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $reviewSeasons = [
            ['start_month' => 1, 'start_year' => 2020, 'end_month' => 6, 'end_year' => 2020, 'is_active' => false],
            ['start_month' => 7, 'start_year' => 2020, 'end_month' => 12, 'end_year' => 2020, 'is_active' => false],
            ['start_month' => 1, 'start_year' => 2021, 'end_month' => 6, 'end_year' => 2021, 'is_active' => false],
            ['start_month' => 7, 'start_year' => 2021, 'end_month' => 12, 'end_year' => 2021, 'is_active' => false],
            ['start_month' => 1, 'start_year' => 2022, 'end_month' => 6, 'end_year' => 2022, 'is_active' => false],
            ['start_month' => 7, 'start_year' => 2022, 'end_month' => 12, 'end_year' => 2022, 'is_active' => false],
            ['start_month' => 1, 'start_year' => 2023, 'end_month' => 6, 'end_year' => 2023, 'is_active' => false],
            ['start_month' => 7, 'start_year' => 2023, 'end_month' => 12, 'end_year' => 2023, 'is_active' => false],
            ['start_month' => 1, 'start_year' => 2024, 'end_month' => 6, 'end_year' => 2024, 'is_active' => false],
            ['start_month' => 7, 'start_year' => 2024, 'end_month' => 12, 'end_year' => 2024, 'is_active' => true],
        ];

        foreach ($reviewSeasons as $season) {
            ReviewSeason::firstOrCreate(
                [
                    'start_month' => $season['start_month'],
                    'start_year' => $season['start_year'],
                ],
                [
                    'end_month' => $season['end_month'],
                    'end_year' => $season['end_year'],
                    'is_active' => $season['is_active'],
                    'set_by_user_id' => $user?->id,
                ]
            );
        }
    }
}
