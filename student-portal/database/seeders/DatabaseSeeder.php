<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.test'],
            [
                'username' => 'localadmin',
                'fullname' => 'Local Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('ChangeMe!12345'),
                'class' => 'Computer Science',
                'year' => '4',
                'department' => 'Computer Science and Engineering',
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'student@example.test'],
            [
                'username' => 'localstudent',
                'fullname' => 'Local Student',
                'email_verified_at' => now(),
                'password' => Hash::make('ChangeMe!12345'),
                'index_number' => 'DEV000001',
                'class' => 'Computer Science',
                'year' => '1',
                'department' => 'Computer Science and Engineering',
                'role' => 'student',
            ]
        );
    }
}
