<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateFirstAdmin extends Command
{
    protected $signature = 'admin:create-first';

    protected $description = 'Create the first administrator account';

    public function handle(): int
    {
        if (User::query()->where('role', 'admin')->exists()) {
            $this->error('An administrator account already exists. Sign in and invite additional administrators from the admin profile.');

            return self::FAILURE;
        }

        $data = [
            'fullname' => $this->ask('Full name'),
            'username' => $this->ask('Username'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Password'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];

        $validator = Validator::make($data, [
            'fullname' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::query()->create([
            'fullname' => $data['fullname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'admin',
            'class' => 'Computer Science',
            'year' => '1',
            'email_verified_at' => now(),
        ]);

        $this->info('First administrator created. Sign in at /admin/login.');

        return self::SUCCESS;
    }
}