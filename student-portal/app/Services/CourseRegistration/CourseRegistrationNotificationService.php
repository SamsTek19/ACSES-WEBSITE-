<?php

namespace App\Services\CourseRegistration;

use App\Mail\Student\CourseRegistrationStatusUpdatedMail;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Mail;

class CourseRegistrationNotificationService
{
    public function notifyStatusChange(CourseRegistration $registration, ?string $previousStatus = null): void
    {
        $registration->loadMissing('student');
        $student = $registration->student;

        if (! $student || empty($student->email)) {
            return;
        }

        if ($previousStatus !== null && $previousStatus === $registration->status) {
            return;
        }

        Mail::to($student->email)->queue(
            new CourseRegistrationStatusUpdatedMail($registration, $previousStatus)
        );
    }
}
