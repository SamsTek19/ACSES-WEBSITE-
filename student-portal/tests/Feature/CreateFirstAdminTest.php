<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreateFirstAdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table): void {
            $table->increments('user_id');
            $table->string('username')->unique();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('class');
            $table->string('year');
            $table->string('role');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_it_creates_the_first_admin_with_a_hidden_password_prompt(): void
    {
        $this->artisan('admin:create-first')
            ->expectsQuestion('Full name', 'First Admin')
            ->expectsQuestion('Username', 'firstadmin')
            ->expectsQuestion('Email', 'admin@example.com')
            ->expectsQuestion('Password', 'a-long-secure-password')
            ->expectsQuestion('Confirm password', 'a-long-secure-password')
            ->expectsOutputToContain('First administrator created')
            ->assertExitCode(0);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(password_verify('a-long-secure-password', $admin->password));
    }

    public function test_it_refuses_to_create_a_second_first_admin(): void
    {
        User::query()->create([
            'fullname' => 'Existing Admin',
            'username' => 'existingadmin',
            'email' => 'existing@example.com',
            'password' => 'password',
            'class' => 'Computer Science',
            'year' => '1',
            'role' => 'admin',
        ]);

        $this->artisan('admin:create-first')
            ->expectsOutputToContain('An administrator account already exists')
            ->assertExitCode(1);

        $this->assertSame(1, User::query()->where('role', 'admin')->count());
    }
}