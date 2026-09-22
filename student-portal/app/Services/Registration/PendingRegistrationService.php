<?php

namespace App\Services\Registration;

use App\Mail\PendingRegistration\ApplicationApprovedMail;
use App\Mail\PendingRegistration\ApplicationReceivedMail;
use App\Mail\PendingRegistration\ApplicationRejectedMail;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\Admin\AdminDueService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PendingRegistrationService
{
    public function __construct(
        private readonly AdminDueService $dueService,
        private readonly StudentEmailValidator $emailValidator,
    ) {
    }

    /**
     * Create a new pending registration and send confirmation email.
     */
    public function create(array $data): PendingRegistration
    {
        $registration = PendingRegistration::create([
            'fullname' => $data['fullname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // Will be hashed by model cast
            'phone_number' => $data['phone_number'] ?? null,
            'index_number' => $data['index_number'],
            'class' => $data['class'],
            'year' => $data['year'],
            'status' => 'pending',
        ]);

        // Send immediate confirmation email
        $this->sendApplicationReceivedEmail($registration);

        return $registration;
    }

    /**
     * Approve a pending registration and create the user account.
     *
     * @throws \Exception if user already exists
     */
    public function approve(PendingRegistration $registration, User $admin): User
    {
        // Check for duplicates before creating
        $existingEmail = User::where('email', $registration->email)->first();
        $existingIndex = User::where('index_number', $registration->index_number)->first();
        $existingUsername = User::where('username', $registration->username)->first();

        if ($existingEmail) {
            throw new \Exception('A user with this email already exists.');
        }

        if ($existingIndex) {
            throw new \Exception('A user with this reference number already exists.');
        }

        if ($existingUsername) {
            throw new \Exception('A user with this username already exists.');
        }

        return DB::transaction(function () use ($registration, $admin) {
            // Create the user account
            $user = User::create([
                'fullname' => $registration->fullname,
                'username' => $registration->username,
                'email' => $registration->email,
                'password' => $registration->password, // Already hashed
                'phone_number' => $registration->phone_number,
                'index_number' => $registration->index_number,
                'class' => $registration->class,
                'year' => $registration->year,
                'role' => 'student',
                'email_verified_at' => Carbon::now(), // Auto-verify since admin approved
            ]);

            // Update registration status
            $registration->update([
                'status' => 'approved',
                'reviewed_by' => $admin->user_id,
                'reviewed_at' => Carbon::now(),
            ]);

            // Sync dues for the new student
            $this->dueService->syncStudent($user);

            // Send approval email with credentials
            $this->sendApprovalEmail($registration, $user);

            return $user;
        });
    }

    /**
     * Reject a pending registration with a reason.
     */
    public function reject(PendingRegistration $registration, User $admin, string $reason): void
    {
        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $admin->user_id,
            'reviewed_at' => Carbon::now(),
        ]);

        // Send rejection email with reason
        $this->sendRejectionEmail($registration);
    }

    /**
     * Bulk approve registrations.
     *
     * @return array{approved: int, failed: array}
     */
    public function bulkApprove(array $ids, User $admin): array
    {
        $approved = 0;
        $failed = [];

        foreach ($ids as $id) {
            $registration = PendingRegistration::find($id);

            if (! $registration) {
                $failed[] = ['id' => $id, 'reason' => 'Registration not found'];
                continue;
            }

            try {
                $this->approve($registration, $admin);
                $approved++;
            } catch (\Exception $e) {
                $failed[] = ['id' => $id, 'reason' => $e->getMessage()];
            }
        }

        return ['approved' => $approved, 'failed' => $failed];
    }

    /**
     * Bulk reject registrations.
     */
    public function bulkReject(array $ids, User $admin, string $reason): int
    {
        $rejected = 0;

        foreach ($ids as $id) {
            $registration = PendingRegistration::find($id);

            if ($registration) {
                $this->reject($registration, $admin, $reason);
                $rejected++;
            }
        }

        return $rejected;
    }

    /**
     * Re-approve a previously rejected registration.
     */
    public function reApprove(PendingRegistration $registration, User $admin): User
    {
        if ($registration->isApproved()) {
            throw new \Exception('This registration has already been approved.');
        }

        // Reset rejection reason before approving
        $registration->rejection_reason = null;

        return $this->approve($registration, $admin);
    }

    /**
     * Check if a user can be created from this registration.
     */
    public function canCreateUser(PendingRegistration $registration): array
    {
        $issues = [];

        if (User::where('email', $registration->email)->exists()) {
            $issues[] = 'Email already exists';
        }

        if (User::where('index_number', $registration->index_number)->exists()) {
            $issues[] = 'Reference number already exists';
        }

        if (User::where('username', $registration->username)->exists()) {
            $issues[] = 'Username already exists';
        }

        return [
            'canCreate' => empty($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Get statistics for pending registrations.
     * Only counts registrations where email has been verified.
     */
    public function getStatistics(): array
    {
        return [
            'pending' => PendingRegistration::readyForReview()->count(),
            'approved' => PendingRegistration::approved()->count(),
            'rejected' => PendingRegistration::rejected()->count(),
            'total' => PendingRegistration::whereNotNull('email_verified_at')->count(),
        ];
    }

    /**
     * Send application received confirmation email.
     */
    private function sendApplicationReceivedEmail(PendingRegistration $registration): void
    {
        // Send immediately, not queued
        Mail::to($registration->email)->send(new ApplicationReceivedMail($registration));
    }

    /**
     * Send approval email with credentials.
     */
    private function sendApprovalEmail(PendingRegistration $registration, User $user): void
    {
        // Send immediately, not queued
        Mail::to($registration->email)->send(new ApplicationApprovedMail($registration, $user));
    }

    /**
     * Send rejection email with reason.
     */
    private function sendRejectionEmail(PendingRegistration $registration): void
    {
        // Send immediately, not queued
        Mail::to($registration->email)->send(new ApplicationRejectedMail($registration));
    }
}
