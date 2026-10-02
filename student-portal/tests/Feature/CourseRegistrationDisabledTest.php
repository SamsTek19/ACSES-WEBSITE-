<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CourseRegistrationDisabledTest extends TestCase
{
    public function test_course_registration_routes_are_not_registered(): void
    {
        $routeNames = [
            'student.course-registration.show',
            'student.course-registration.store',
            'student.course-registration.documents.destroy',
            'admin.course-registrations.index',
            'admin.course-registrations.show',
            'admin.course-registrations.update',
            'admin.course-registrations.bulk',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertFalse(Route::has($routeName), "Route {$routeName} should be disabled.");
        }
    }

    public function test_student_account_application_review_remains_available(): void
    {
        $this->assertTrue(Route::has('admin.pending-registrations.index'));
    }
}