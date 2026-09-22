<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Models\TrustedDevice;
use App\Models\User;
use App\Services\Auth\DeviceDetectionService;
use App\Services\Student\StudentEmailChangeService;
use App\Services\Student\StudentProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $profileService,
        private readonly StudentEmailChangeService $emailChangeService,
        private readonly DeviceDetectionService $deviceDetectionService
    )
    {
    }

    public function show(Request $request): View
    {
        $student = $request->user('student');
        
        // Get trusted devices for this user
        $trustedDevices = $this->deviceDetectionService->getUserDevices($student);
        
        // Identify current device fingerprint for highlighting
        $currentFingerprint = $this->deviceDetectionService->generateFingerprint($request);

        return view('dashboards.student.profile', [
            'title' => 'My Profile',
            'student' => $student,
            'trustedDevices' => $trustedDevices,
            'currentFingerprint' => $currentFingerprint,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $student = $request->user('student');

        $result = $this->profileService->update($student, $request->validated());

        $student->refresh();

        $messages = [];

        if ($result['profile_updated']) {
            $messages[] = __('Contact details updated.');
        }

        if ($result['password_updated']) {
            $messages[] = __('Password updated successfully.');
        }

        $emailStatus = $result['email_status'];

        if ($emailStatus) {
            $pending = $student->pending_email;
            $messages[] = match ($emailStatus) {
                'initiated' => __('We sent a verification link to :email. Please check your inbox within the next hour.', ['email' => $pending]),
                'resent' => __('Verification link resent to :email.', ['email' => $pending]),
                'pending' => __('Your email change to :email is still awaiting verification.', ['email' => $pending]),
                'cancelled' => __('Pending email change cancelled.'),
                default => null,
            };
        }

        $messages = array_filter($messages);

        if (empty($messages)) {
            $messages[] = __('No changes were made.');
        }

        return redirect()->route('student.profile')
            ->with('status', implode(' ', $messages));
    }

    public function verifyEmail(Request $request, User $user, string $token): RedirectResponse
    {
        if ($request->hasValidSignature() === false) {
            abort(403);
        }

        if (! $this->emailChangeService->confirm($user, $token)) {
            return redirect()->route('auth.login')
                ->withErrors(['verification' => __('We could not verify that email change request. Please start again from your profile settings.')]);
        }

        if (Auth::guard('student')->check() && Auth::guard('student')->id() === $user->getKey()) {
            $request->session()->flash('status', __('Your email address has been updated and verified.'));

            return redirect()->route('student.profile');
        }

        return redirect()->route('auth.login')
            ->with('status', __('Email updated successfully. Please sign in with your new address.'));
    }

    /**
     * Revoke a specific trusted device.
     */
    public function revokeDevice(Request $request, TrustedDevice $device): RedirectResponse
    {
        $student = $request->user('student');

        // Ensure the device belongs to this user
        if ($device->user_id !== $student->user_id) {
            abort(403);
        }

        $deviceName = $device->device_name;
        $device->delete();

        return redirect()->route('student.profile')
            ->with('status', __('Device ":device" has been removed from your trusted devices.', ['device' => $deviceName]));
    }

    /**
     * Revoke all trusted devices except the current one.
     */
    public function revokeAllDevices(Request $request): RedirectResponse
    {
        $student = $request->user('student');
        $currentFingerprint = $this->deviceDetectionService->generateFingerprint($request);

        // Delete all devices except current
        TrustedDevice::where('user_id', $student->user_id)
            ->where('device_fingerprint', '!=', $currentFingerprint)
            ->delete();

        return redirect()->route('student.profile')
            ->with('status', __('All other trusted devices have been removed. You will need to verify them again on next login.'));
    }
}

