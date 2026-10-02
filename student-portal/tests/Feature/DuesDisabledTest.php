<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DuesDisabledTest extends TestCase
{
    public function test_dues_and_payment_routes_are_not_registered(): void
    {
        $routeNames = [
            'student.dues.index',
            'student.payments.paystack.initialize',
            'student.payments.paystack.callback',
            'student.payments.rushpay.initialize',
            'student.payments.rushpay.checkout',
            'student.payments.manual.submit',
            'admin.dues.index',
            'admin.dues.verifications.index',
            'admin.maintenance.sync-missing',
            'admin.maintenance.update-amounts',
            'admin.maintenance.merge-dues',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertFalse(Route::has($routeName), "Route {$routeName} should be disabled.");
        }
    }

    public function test_student_dashboard_does_not_use_the_dues_gate(): void
    {
        $dashboard = Route::getRoutes()->getByName('student.dashboard');

        $this->assertNotNull($dashboard);
        $this->assertContains('auth:student', $dashboard->gatherMiddleware());
        $this->assertNotContains('student.no_outstanding_dues', $dashboard->gatherMiddleware());
    }
}