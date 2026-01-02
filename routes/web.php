<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\User as ProfileUser;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
        
    Route::get('profile/{user}/{section?}', function (ProfileUser $user, $section = null) {
        return view('profile', compact('user', 'section'));
    })
        ->middleware('can:manage,user')
        ->name('profile.show.section');

    Route::get('profile/{user}', function (ProfileUser $user) {
        return view('profile', compact('user'));
    })
        ->middleware('can:manage,user')
        ->name('profile.show.other');

    Route::get('profile', function () {
        return view('profile');
    })->name('profile.show');

    Route::get('profiles', function () {
        $users = ProfileUser::orderBy('name')->get();
        return view('pages.profiles.index', compact('users'));
    })
        ->middleware(['auth'])
        ->name('profiles.index');

    Route::get('profiles/{user}/{section?}', function (ProfileUser $user, $section = null) {
        return view('pages.profiles.show', compact('user', 'section'));
    })
        ->middleware('can:manage,user')
        ->name('profiles.show.section');

    Route::get('profiles/{user}', function (ProfileUser $user) {
        return view('pages.profiles.show', compact('user'));
    })
        ->middleware('can:manage,user')
        ->name('profiles.show.other');
        
    Route::view('database/provinces', 'pages.reference_tables.provinces')
        ->middleware(['auth'])
        ->name('pages.reference_tables.provinces');
    Route::view('database/cities', 'pages.reference_tables.cities')
        ->middleware(['auth'])
        ->name('pages.reference_tables.cities');
    Route::view('database/barangays', 'pages.reference_tables.barangays')
        ->middleware(['auth'])
        ->name('pages.reference_tables.barangays');
    Route::view('database', 'database')
        ->middleware(['auth'])
        ->name('database');
    Route::view('database/degree_fields', 'pages.reference_tables.degree_fields')
        ->middleware(['auth'])
        ->name('pages.reference_tables.degree_fields');
    Route::view('database/degree_levels', 'pages.reference_tables.degree_levels')
        ->middleware(['auth'])
        ->name('pages.reference_tables.degree_levels');
    Route::view('database/degree_programs', 'pages.reference_tables.degree_programs')
        ->middleware(['auth'])
        ->name('pages.reference_tables.degree_programs');
    Route::view('database/degree_types', 'pages.reference_tables.degree_types')
        ->middleware(['auth'])
        ->name('pages.reference_tables.degree_types');
    Route::view('database/universities', 'pages.reference_tables.universities')
        ->middleware(['auth'])
        ->name('pages.reference_tables.universities');

    Route::view('database/highschools', 'pages.reference_tables.highschools')
        ->middleware(['auth'])
        ->name('pages.reference_tables.highschools');
    Route::view('database/highschool_subjects', 'pages.reference_tables.highschool_subjects')
        ->middleware(['auth'])
        ->name('pages.reference_tables.highschool_subjects');
    Route::view('database/fields_of_work', 'pages.reference_tables.fields_of_work')
        ->middleware(['auth'])
        ->name('pages.reference_tables.fields_of_work');
    Route::view('database/prefix_titles', 'pages.reference_tables.prefix_titles')
        ->middleware(['auth'])
        ->name('pages.reference_tables.prefix_titles');
    Route::view('database/suffix_titles', 'pages.reference_tables.suffix_titles')
        ->middleware(['auth'])
        ->name('pages.reference_tables.suffix_titles');

    Route::view('database/volunteer_subjects', 'pages.reference_tables.volunteer_subjects')
        ->middleware(['auth'])
        ->name('pages.reference_tables.volunteer_subjects');
    Route::view('database/positions', 'pages.reference_tables.committee_positions')
        ->middleware(['auth'])
        ->name('pages.reference_tables.committee_positions');
    Route::view('database/committees', 'pages.reference_tables.committees')
        ->middleware(['auth'])
        ->name('pages.reference_tables.committees');
    Route::view('database/classrooms', 'pages.reference_tables.classrooms')
        ->middleware(['auth'])
        ->name('pages.reference_tables.classrooms');
    Route::view('database/classroom_positions', 'pages.reference_tables.classroom_positions')
        ->middleware(['auth'])
        ->name('pages.reference_tables.classroom_positions');

    Route::view('database/review_seasons', 'pages.reference_tables.review_seasons')
        ->middleware(['auth'])
        ->name('pages.reference_tables.review_seasons');
    Route::view('database/fceer_batches', 'pages.reference_tables.fceer_batches')
        ->middleware(['auth'])
        ->name('pages.reference_tables.fceer_batches');
    Route::view('database/user_attendance_statuses', 'pages.reference_tables.user_attendance_statuses')
        ->middleware(['auth'])
        ->name('pages.reference_tables.user_attendance_statuses');
    Route::view('database/user_roles', 'pages.reference_tables.user_roles')
        ->middleware(['auth'])
        ->name('pages.reference_tables.user_roles');

    Route::get('references/export', [\App\Http\Controllers\ReferenceExportController::class, 'export'])
        ->middleware(['auth'])
        ->name('references.export');

    // Roster pages
    Route::middleware(['auth', 'can:viewRoster'])->group(function () {
        Route::view('roster/volunteers', 'pages.roster.volunteers')
            ->name('roster.volunteers');
        Route::view('roster/students', 'pages.roster.students')
            ->name('roster.students');
    });

    // Attendance pages
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Roster views (require viewAnyAttendance permission)
        Route::middleware(['can:viewAnyAttendance'])->group(function () {
            Route::view('students', 'pages.attendance.students')
                ->name('students');
            Route::view('volunteers', 'pages.attendance.volunteers')
                ->name('volunteers');
        });

        // Individual user attendance (own or with permission)
        Route::get('user/{userId?}', function ($userId = null) {
            return view('pages.attendance.user', ['userId' => $userId]);
        })->name('user');
    });

    // Review Season Management
    Route::view('reviewseason', 'pages.reviewseason')
        ->middleware(['can:manageReviewSeason'])
        ->name('reviewseason');
});
