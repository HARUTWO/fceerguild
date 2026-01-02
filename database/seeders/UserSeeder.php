<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get user roles
        $studentRole = UserRole::firstOrCreate(['name' => 'Student'], ['description' => 'Student user']);
        $adminRole = UserRole::firstOrCreate(['name' => 'Administrator'], ['description' => 'Administrator user']);
        $executiveRole = UserRole::firstOrCreate(['name' => 'Executive'], ['description' => 'Executive user']);
        $systemManagerRole = UserRole::firstOrCreate(['name' => 'System Manager'], ['description' => 'System Manager user']);
        $instructorRole = UserRole::firstOrCreate(['name' => 'Instructor'], ['description' => 'Instructor user']);

        // 10 Students
        $students = [
            ['name' => 'Juan Santos', 'username' => 'jsantos'],
            ['name' => 'Maria Garcia', 'username' => 'mgarcia'],
            ['name' => 'Carlos Reyes', 'username' => 'creyes'],
            ['name' => 'Ana Diaz', 'username' => 'adiaz'],
            ['name' => 'Pedro Cruz', 'username' => 'pcruz'],
            ['name' => 'Rosa Fernandez', 'username' => 'rfernandez'],
            ['name' => 'Miguel Torres', 'username' => 'mtorres'],
            ['name' => 'Isabel Lopez', 'username' => 'ilopez'],
            ['name' => 'Antonio Morales', 'username' => 'amorales'],
            ['name' => 'Luisa Rivera', 'username' => 'lrivera'],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['email' => $student['username'] . '@fceer.edu.ph'],
                [
                    'name' => $student['name'],
                    'password' => Hash::make($student['username'] . 'password'),
                    'role_id' => $studentRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        // 5 Administrators
        $administrators = [
            ['name' => 'Admin One', 'username' => 'adminone'],
            ['name' => 'Admin Two', 'username' => 'admintwo'],
            ['name' => 'Admin Three', 'username' => 'adminthree'],
            ['name' => 'Admin Four', 'username' => 'adminfour'],
            ['name' => 'Admin Five', 'username' => 'adminfive'],
        ];

        foreach ($administrators as $admin) {
            User::firstOrCreate(
                ['email' => $admin['username'] . '@fceer.edu.ph'],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['username'] . 'password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        // 5 Executives
        $executives = [
            ['name' => 'Executive One', 'username' => 'execone'],
            ['name' => 'Executive Two', 'username' => 'exectwo'],
            ['name' => 'Executive Three', 'username' => 'execthree'],
            ['name' => 'Executive Four', 'username' => 'execfour'],
            ['name' => 'Executive Five', 'username' => 'execfive'],
        ];

        foreach ($executives as $executive) {
            User::firstOrCreate(
                ['email' => $executive['username'] . '@fceer.edu.ph'],
                [
                    'name' => $executive['name'],
                    'password' => Hash::make($executive['username'] . 'password'),
                    'role_id' => $executiveRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        // 2 System Managers
        $systemManagers = [
            ['name' => 'System Manager One', 'username' => 'sysmanagerone'],
            ['name' => 'System Manager Two', 'username' => 'sysmanagertwo'],
        ];

        foreach ($systemManagers as $manager) {
            User::firstOrCreate(
                ['email' => $manager['username'] . '@fceer.edu.ph'],
                [
                    'name' => $manager['name'],
                    'password' => Hash::make($manager['username'] . 'password'),
                    'role_id' => $systemManagerRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }

        // 5 Instructors
        $instructors = [
            ['name' => 'Instructor One', 'username' => 'instrone'],
            ['name' => 'Instructor Two', 'username' => 'instrtwo'],
            ['name' => 'Instructor Three', 'username' => 'instrthree'],
            ['name' => 'Instructor Four', 'username' => 'instrfour'],
            ['name' => 'Instructor Five', 'username' => 'instrfive'],
        ];

        foreach ($instructors as $instructor) {
            User::firstOrCreate(
                ['email' => $instructor['username'] . '@fceer.edu.ph'],
                [
                    'name' => $instructor['name'],
                    'password' => Hash::make($instructor['username'] . 'password'),
                    'role_id' => $instructorRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }
    }
}
