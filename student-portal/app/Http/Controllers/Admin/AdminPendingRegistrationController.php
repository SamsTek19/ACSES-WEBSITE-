<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Services\Registration\PendingRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPendingRegistrationController extends Controller
{
    public function __construct(
        private readonly PendingRegistrationService $registrationService,
    ) {
    }

    /**
     * Display a listing of pending registrations.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));
        $class = trim((string) $request->query('class'));
        $year = trim((string) $request->query('year'));
        $perPage = (int) $request->query('per_page', 25);
        $perPageOptions = [25, 50, 100, 250];

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 25;
        }

        $statuses = ['pending', 'approved', 'rejected'];
        $classOptions = ['Cyber Security', 'Computer Science', 'Information System', 'Robotics'];
        $yearOptions = ['1', '2', '3', '4'];

        $registrations = PendingRegistration::query()
            // Only show registrations where email has been verified
            ->whereNotNull('email_verified_at')
            ->when($status && in_array($status, $statuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('fullname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('index_number', 'like', "%{$search}%");
                });
            })
            ->when($class !== '', function ($query) use ($class) {
                $query->where('class', $class);
            })
            ->when($year !== '', function ($query) use ($year) {
                $query->where('year', $year);
            })
            ->with('reviewer:user_id,fullname,username')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends([
                'status' => $status,
                'search' => $search,
                'class' => $class,
                'year' => $year,
                'per_page' => $perPage,
            ]);

        $statistics = $this->registrationService->getStatistics();
        
        // Also count unverified registrations awaiting email verification
        $awaitingEmailVerification = PendingRegistration::awaitingEmailVerification()->count();

        return view('dashboards.admin.pending-registrations.index', [
            'title' => 'Pending Registrations',
            'registrations' => $registrations,
            'statuses' => $statuses,
            'activeStatus' => $status,
            'search' => $search,
            'activeClass' => $class,
            'activeYear' => $year,
            'classOptions' => $classOptions,
            'yearOptions' => $yearOptions,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
            'statistics' => $statistics,
            'awaitingEmailVerification' => $awaitingEmailVerification,
        ]);
    }

    /**
     * Approve a pending registration.
     */
    public function approve(Request $request, PendingRegistration $registration): RedirectResponse
    {
        if (! $registration->isPending() && ! $registration->isRejected()) {
            return redirect()->back()
                ->withErrors(['error' => __('This registration has already been processed.')]);
        }

        try {
            $admin = Auth::guard('admin')->user();
            $this->registrationService->approve($registration, $admin);

            return redirect()->back()
                ->with('status', __('Registration approved successfully. The student has been notified via email.'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reject a pending registration.
     */
    public function reject(Request $request, PendingRegistration $registration): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        if (! $registration->isPending() && ! $registration->isApproved()) {
            return redirect()->back()
                ->withErrors(['error' => __('This registration has already been processed.')]);
        }

        $admin = Auth::guard('admin')->user();
        $this->registrationService->reject($registration, $admin, $request->input('rejection_reason'));

        return redirect()->back()
            ->with('status', __('Registration rejected. The student has been notified via email.'));
    }

    /**
     * Handle bulk actions on registrations.
     */
    public function bulk(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:pending_registrations,id'],
            'action' => ['required', 'string', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $admin = Auth::guard('admin')->user();

        if ($action === 'approve') {
            $result = $this->registrationService->bulkApprove($ids, $admin);

            $message = __(':count registration(s) approved successfully.', ['count' => $result['approved']]);

            if (! empty($result['failed'])) {
                $failedCount = count($result['failed']);
                $message .= ' ' . __(':count failed due to duplicate accounts.', ['count' => $failedCount]);
            }

            return redirect()->back()
                ->with('status', $message);
        }

        if ($action === 'reject') {
            $reason = $request->input('rejection_reason');
            $count = $this->registrationService->bulkReject($ids, $admin, $reason);

            return redirect()->back()
                ->with('status', __(':count registration(s) rejected.', ['count' => $count]));
        }

        return redirect()->back();
    }

    /**
     * Show details of a specific registration.
     */
    public function show(PendingRegistration $registration): View
    {
        $registration->load('reviewer:user_id,fullname,username');

        // Check if user can be created
        $canCreateUser = $this->registrationService->canCreateUser($registration);

        return view('dashboards.admin.pending-registrations.show', [
            'title' => 'Registration Details',
            'registration' => $registration,
            'canCreateUser' => $canCreateUser,
        ]);
    }
}
