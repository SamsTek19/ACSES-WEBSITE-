<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentPaystackPaymentController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Admin\AdminCourseRegistrationController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminSuggestionController;
use App\Http\Controllers\Admin\AdminDueController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminAcademicTimelineController;

Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::view('/legal/terms', 'legal.terms')->name('legal.terms');
Route::view('/legal/privacy', 'legal.privacy')->name('legal.privacy');
Route::view('/legal/cookies', 'legal.cookies')->name('legal.cookies');

Route::view('/developers', 'developers')->name('marketing.developers');
Route::view('/legal/accessibility', 'legal.accessibility')->name('legal.accessibility');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:auth-login')
        ->name('auth.login.submit');

    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:auth-password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->middleware('throttle:auth-password-reset')
        ->name('password.update');

    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])
        ->name('auth.register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])
        ->middleware('throttle:auth-register')
        ->name('auth.register.submit');
    Route::post('/register/check-availability', [\App\Http\Controllers\Auth\RegisterController::class, 'checkAvailability'])
        ->middleware('throttle:auth-check-availability')
        ->name('auth.register.check-availability');

    Route::get('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])
        ->name('auth.verify.notice');
    Route::post('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware('throttle:auth-otp')
        ->name('auth.verify.submit');
    Route::post('/verify-email/resend', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])
        ->middleware('throttle:auth-resend')
        ->name('auth.verify.resend');
    Route::get('/verify-email/resend', function () {
        return redirect()->route('auth.verify.notice');
    });

    Route::get('/login/otp', [\App\Http\Controllers\Auth\LoginOtpController::class, 'create'])
        ->name('auth.login.otp');
    Route::post('/login/otp', [\App\Http\Controllers\Auth\LoginOtpController::class, 'store'])
        ->middleware('throttle:auth-otp')
        ->name('auth.login.otp.submit');
    Route::post('/login/otp/resend', [\App\Http\Controllers\Auth\LoginOtpController::class, 'resend'])
        ->middleware('throttle:auth-resend')
        ->name('auth.login.otp.resend');

    // Pending registration email verification
    Route::get('/register/verify-email', [\App\Http\Controllers\Auth\PendingRegistrationVerificationController::class, 'show'])
        ->name('auth.pending-registration.verify');
    Route::post('/register/verify-email', [\App\Http\Controllers\Auth\PendingRegistrationVerificationController::class, 'verify'])
        ->middleware('throttle:auth-otp')
        ->name('auth.pending-registration.verify.submit');
    Route::post('/register/verify-email/resend', [\App\Http\Controllers\Auth\PendingRegistrationVerificationController::class, 'resend'])
        ->middleware('throttle:auth-resend')
        ->name('auth.pending-registration.verify.resend');
});

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

Route::get('/student/dashboard', StudentDashboardController::class)
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.dashboard');

Route::get('/student/profile', [StudentProfileController::class, 'show'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.profile');

Route::post('/student/profile', [StudentProfileController::class, 'update'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.profile.update');

Route::get('/student/profile/verify-email/{user}/{token}', [StudentProfileController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('student.profile.verify-email');

Route::delete('/student/profile/devices/{device}', [StudentProfileController::class, 'revokeDevice'])
    ->middleware('auth:student')
    ->name('student.profile.devices.revoke');

Route::delete('/student/profile/devices', [StudentProfileController::class, 'revokeAllDevices'])
    ->middleware('auth:student')
    ->name('student.profile.devices.revoke-all');

Route::get('/student/suggestions', [\App\Http\Controllers\Student\StudentSuggestionController::class, 'index'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.suggestions.index');

Route::post('/student/suggestions', [\App\Http\Controllers\Student\StudentSuggestionController::class, 'store'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.suggestions.store');

Route::get('/student/announcements', [\App\Http\Controllers\Student\StudentAnnouncementController::class, 'index'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.announcements.index');

Route::get('/student/announcements/{announcement:slug}', [\App\Http\Controllers\Student\StudentAnnouncementController::class, 'show'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.announcements.show');

Route::get('/student/events', [\App\Http\Controllers\Student\StudentEventController::class, 'index'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.events.index');

Route::get('/student/events/{event}/ics', [\App\Http\Controllers\Student\StudentEventController::class, 'ics'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.events.ics');

Route::get('/student/resources', [\App\Http\Controllers\Student\StudentResourceController::class, 'index'])
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.resources.index');

Route::get('/student/dues', [\App\Http\Controllers\Student\StudentDueController::class, 'index'])
    ->middleware('auth:student')
    ->name('student.dues.index');

Route::post('/student/dues/{due}/paystack', [StudentPaystackPaymentController::class, 'initialize'])
    ->middleware('auth:student')
    ->name('student.payments.paystack.initialize');

Route::get('/student/payments/paystack/callback', [StudentPaystackPaymentController::class, 'callback'])
    ->middleware('auth:student')
    ->name('student.payments.paystack.callback');

Route::get('/student/dues/{due}/receipt', [StudentPaystackPaymentController::class, 'receipt'])
    ->middleware('auth:student')
    ->name('student.payments.paystack.receipt');

// RushPay Integration
Route::post('/student/dues/{due}/rushpay', [\App\Http\Controllers\Student\StudentRushPayPaymentController::class, 'initialize'])
    ->middleware('auth:student')
    ->name('student.payments.rushpay.initialize');

Route::get('/student/payments/rushpay/checkout/{reference}', [\App\Http\Controllers\Student\StudentRushPayPaymentController::class, 'checkout'])
    ->middleware('auth:student')
    ->name('student.payments.rushpay.checkout');

Route::get('/student/payments/rushpay/callback', [\App\Http\Controllers\Student\StudentRushPayPaymentController::class, 'callback'])
    ->middleware('auth:student')
    ->name('student.payments.rushpay.callback');

Route::post('/student/dues/{due}/manual-pay', [\App\Http\Controllers\Student\StudentManualPaymentController::class, 'store'])
    ->middleware(['auth:student', 'throttle:6,1'])
    ->name('student.payments.manual.submit');

Route::get('/student/course-registration', function () {
    return redirect()
        ->route('student.dashboard')
        ->with('status', __('Course registration is currently disabled.'));
})
    ->middleware(['auth:student', 'student.no_outstanding_dues'])
    ->name('student.course-registration.show');

Route::post('/student/course-registration', function () {
    abort(404);
})
    ->middleware('auth:student')
    ->name('student.course-registration.store');

Route::delete('/student/course-registration/documents/{registration}/{encodedPath}', function () {
    abort(404);
})
    ->middleware('auth:student')
    ->name('student.course-registration.documents.destroy');

Route::get('/admin/dashboard', \App\Http\Controllers\Admin\AdminDashboardController::class)
    ->middleware('auth:admin')
    ->name('admin.dashboard');

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('events', \App\Http\Controllers\Admin\AdminEventController::class)->except(['show']);
    Route::resource('resources', \App\Http\Controllers\Admin\AdminResourceController::class)->except(['show']);
    Route::resource('announcements', AdminAnnouncementController::class)->except(['show']);
    Route::resource('timeline', AdminAcademicTimelineController::class)->except(['show']);
    Route::get('dues/export', [AdminDueController::class, 'export'])->name('dues.export');
    Route::post('students/promote-years', [\App\Http\Controllers\Admin\AdminStudentAccountController::class, 'promoteYears'])->name('students.promote-years');
    Route::get('dues/statistics', [AdminDueController::class, 'statistics'])->name('dues.statistics');
    Route::resource('dues', AdminDueController::class)->except(['show', 'destroy']);
    
    // Maintenance Portal (Hidden)
    Route::prefix('maintenance')->name('maintenance.')->middleware('can:access-maintenance')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'index'])->name('index');
        Route::post('/delete-orphaned', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'deleteOrphaned'])->name('delete-orphaned');
        Route::post('/resolve-duplicates', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'resolveDuplicates'])->name('resolve-duplicates');
        Route::post('/sync-missing', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'syncMissing'])->name('sync-missing');
        Route::post('/update-amounts', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'updateAmounts'])->name('update-amounts');
        Route::post('/merge-dues', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'mergeDues'])->name('merge-dues');
        Route::post('/delete-dummies', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'deleteDummies'])->name('delete-dummies');
        Route::post('/optimize', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'optimize'])->name('optimize');
        Route::post('/migrate', [\App\Http\Controllers\Admin\AdminMaintenanceController::class, 'migrate'])->name('migrate');
    });

    // Dues Verifications & Settings
    Route::get('dues/verifications', [\App\Http\Controllers\Admin\AdminDueVerificationController::class, 'index'])->name('dues.verifications.index');
    Route::post('dues/verifications/{due}/approve', [\App\Http\Controllers\Admin\AdminDueVerificationController::class, 'approve'])->name('dues.verifications.approve');
    Route::post('dues/verifications/{due}/reject', [\App\Http\Controllers\Admin\AdminDueVerificationController::class, 'reject'])->name('dues.verifications.reject');
    Route::post('dues/settings', [AdminDueController::class, 'updateSettings'])->name('dues.settings.update');

    Route::resource('course-registrations', AdminCourseRegistrationController::class)->only(['index', 'show', 'update']);
    Route::post('course-registrations/bulk', [AdminCourseRegistrationController::class, 'bulk'])->name('course-registrations.bulk');
    Route::post('suggestions/bulk', [AdminSuggestionController::class, 'bulk'])->name('suggestions.bulk');
    Route::resource('suggestions', AdminSuggestionController::class)->only(['index', 'show', 'update']);
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/admins', [AdminProfileController::class, 'storeAdmin'])->name('profile.admins.store');
    Route::post('profile/snapshots', [AdminProfileController::class, 'createSnapshot'])->name('profile.snapshots.store');
    Route::get('profile/snapshots/download/{snapshot}', [AdminProfileController::class, 'downloadSnapshot'])->name('profile.snapshots.download');

    Route::get('students/export', [\App\Http\Controllers\Admin\AdminStudentAccountController::class, 'export'])
        ->name('students.export');
    Route::post('students/promote-years', [\App\Http\Controllers\Admin\AdminStudentAccountController::class, 'promoteYears'])
        ->name('students.promote-years');
    Route::resource('students', \App\Http\Controllers\Admin\AdminStudentAccountController::class);

    // Pending Registrations Management
    Route::get('pending-registrations', [\App\Http\Controllers\Admin\AdminPendingRegistrationController::class, 'index'])
        ->name('pending-registrations.index');
    Route::get('pending-registrations/{registration}', [\App\Http\Controllers\Admin\AdminPendingRegistrationController::class, 'show'])
        ->name('pending-registrations.show');
    Route::post('pending-registrations/{registration}/approve', [\App\Http\Controllers\Admin\AdminPendingRegistrationController::class, 'approve'])
        ->name('pending-registrations.approve');
    Route::post('pending-registrations/{registration}/reject', [\App\Http\Controllers\Admin\AdminPendingRegistrationController::class, 'reject'])
        ->name('pending-registrations.reject');
    Route::post('pending-registrations/bulk', [\App\Http\Controllers\Admin\AdminPendingRegistrationController::class, 'bulk'])
        ->name('pending-registrations.bulk');
});

Route::get('/debug/assets', function () {
    $faviconFiles = [
        'ico' => 'favicon.ico',
        'svg' => 'favicon.svg',
        'png' => 'favicon-96x96.png',
    ];

    $favicons = [];

    foreach ($faviconFiles as $key => $filename) {
        $publicPath = public_path($filename);

        $favicons[$key] = [
            'file' => $filename,
            'asset_url' => asset($filename),
            'public_path' => $publicPath,
            'exists' => file_exists($publicPath),
        ];
    }

    $student = Auth::guard('student')->user();
    $admin = Auth::guard('admin')->user();

    $profiles = [];

    if ($student) {
        $raw = $student->profile_picture;

        $profiles['student'] = [
            'id' => $student->id,
            'raw' => $raw,
            'public_disk_exists' => $raw ? Storage::disk('public')->exists($raw) : null,
            'asset_storage_url' => $raw ? asset('storage/' . ltrim($raw, '/')) : null,
            'storage_url' => $raw ? Storage::disk('public')->url($raw) : null,
        ];
    }

    if ($admin) {
        $raw = $admin->profile_picture;

        $profiles['admin'] = [
            'id' => $admin->id,
            'raw' => $raw,
            'public_disk_exists' => $raw ? Storage::disk('public')->exists($raw) : null,
            'storage_url' => $raw ? Storage::disk('public')->url($raw) : null,
        ];
    }

    $storageLink = public_path('storage');

    $debug = [
        'app_url' => config('app.url'),
        'request_url' => request()->fullUrl(),
        'public_path' => public_path(),
        'storage_public_path' => storage_path('app/public'),
        'storage_symlink_exists' => file_exists($storageLink),
        'storage_symlink_is_link' => is_link($storageLink),
    ];

    return view('debug.assets', [
        'debug' => $debug,
        'favicons' => $favicons,
        'profiles' => $profiles,
    ]);
})->name('debug.assets');
