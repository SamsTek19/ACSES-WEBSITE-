<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_student_login_and_registration_redirect_to_admin_login(): void
    {
        $this->get('/login')->assertRedirect(route('admin.login'));
        $this->get('/register')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk();
    }
}
