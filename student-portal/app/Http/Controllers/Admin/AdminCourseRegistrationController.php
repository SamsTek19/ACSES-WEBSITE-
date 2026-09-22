<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkCourseRegistrationActionRequest;
use App\Http\Requests\Admin\UpdateCourseRegistrationRequest;
use App\Models\CourseRegistration;
use App\Models\User;
use App\Services\CourseRegistration\CourseRegistrationNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AdminCourseRegistrationController extends Controller
{
    private const STATUSES = ['in_progress', 'submitted', 'approved', 'rejected'];

    public function __construct(private readonly CourseRegistrationNotificationService $notificationService)
    {
    }

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

        $classOptions = User::query()
            ->where('role', 'student')
            ->whereNotNull('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class')
            ->filter()
            ->values();

        $yearOptions = User::query()
            ->where('role', 'student')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->filter()
            ->values();

        $registrations = CourseRegistration::query()
            ->with(['student:user_id,fullname,username,email,class,year'])
            ->when($status && in_array($status, self::STATUSES, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('fullname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($class !== '', function ($query) use ($class) {
                $query->whereHas('student', fn ($q) => $q->where('class', $class));
            })
            ->when($year !== '', function ($query) use ($year) {
                $query->whereHas('student', fn ($q) => $q->where('year', $year));
            })
            ->orderByDesc('submitted_at')
            ->orderBy('status')
            ->paginate($perPage)
            ->appends([
                'status' => $status,
                'search' => $search,
                'class' => $class,
                'year' => $year,
                'per_page' => $perPage,
            ]);

        return view('dashboards.admin.course-registrations.index', [
            'title' => 'Course registrations',
            'registrations' => $registrations,
            'statuses' => self::STATUSES,
            'activeStatus' => $status,
            'search' => $search,
            'activeClass' => $class,
            'activeYear' => $year,
            'classOptions' => $classOptions,
            'yearOptions' => $yearOptions,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
        ]);
    }

    public function bulk(BulkCourseRegistrationActionRequest $request): Response
    {
        $validated = $request->validated();
        $ids = $validated['ids'];
        $action = $validated['action'];

        $registrations = CourseRegistration::query()
            ->whereIn('id', $ids)
            ->get();

        if ($registrations->isEmpty()) {
            return redirect()->route('admin.course-registrations.index')
                ->with('status', __('No registrations were selected.'));
        }

        if ($action === 'update_status') {
            $status = $validated['status'];
            $comment = $validated['admin_comment'] ?? null;

            foreach ($registrations as $registration) {
                $previousStatus = $registration->status;
                $registration->status = $status;
                $registration->admin_comment = $comment;
                $registration->progress_percent = match ($status) {
                    'approved', 'rejected' => 100,
                    'submitted' => 90,
                    default => 60,
                };
                $registration->approved_at = $status === 'approved' ? Carbon::now() : null;
                $registration->submitted_at = $status === 'submitted'
                    ? ($registration->submitted_at ?? Carbon::now())
                    : ($status === 'in_progress' ? null : $registration->submitted_at);
                if ($status === 'rejected') {
                    $registration->pending_documents = max(1, (int) $registration->pending_documents);
                }
                $registration->save();
                $this->notificationService->notifyStatusChange($registration, $previousStatus);
            }

            $redirectUrl = $validated['return_url'] ?? null;

            return $redirectUrl
                ? redirect()->to($redirectUrl)->with('status', __('Selected registrations updated successfully.'))
                : redirect()->route('admin.course-registrations.index')
                    ->with('status', __('Selected registrations updated successfully.'));
        }

        if ($action === 'download_documents') {
            $zip = new \ZipArchive();
            $zipFileName = 'course-registrations-' . now()->format('Ymd-His') . '.zip';
            $tempPath = storage_path('app/tmp-' . uniqid('course-reg', false) . '.zip');
            $filesAdded = 0;

            if ($zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return redirect()->route('admin.course-registrations.index')
                    ->withErrors(__('Unable to prepare bulk download archive.'));
            }

            foreach ($registrations as $registration) {
                $documents = $registration->document_paths ?? [];
                foreach ($documents as $path) {
                    $fullPath = Storage::disk('public')->path($path);
                    if (is_file($fullPath)) {
                        $filename = $registration->student?->fullname
                            ?? $registration->student?->username
                            ?? 'student';
                        $safeName = Str::slug($filename ?: 'student');
                        $zip->addFile($fullPath, $safeName . '/' . basename($path));
                        $filesAdded++;
                    }
                }
            }

            $zip->close();

            if ($filesAdded === 0) {
                @unlink($tempPath);

                $redirectUrl = $validated['return_url'] ?? null;

                return $redirectUrl
                    ? redirect()->to($redirectUrl)->withErrors(__('Selected registrations do not have documents to download.'))
                    : redirect()->route('admin.course-registrations.index')
                        ->withErrors(__('Selected registrations do not have documents to download.'));
            }

            return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
        }

        $redirectUrl = $validated['return_url'] ?? null;

        return $redirectUrl
            ? redirect()->to($redirectUrl)
            : redirect()->route('admin.course-registrations.index');
    }

    public function show(Request $request, CourseRegistration $registration)
    {
        if (! $registration->exists) {
            $routeKey = $request->route('course_registration');
            $registration = CourseRegistration::query()->findOrFail($routeKey);
        }

        $registration->load('student');

        $paths = collect($registration->document_paths ?? [])
            ->filter(fn ($p) => is_string($p) && trim($p) !== '')
            ->values()
            ->all();

        \Log::info('Admin single download', [
            'registration_id' => $registration->getKey(),
            'path_count' => count($paths),
        ]);

        if (count($paths) === 0) {
            return redirect()
                ->route('admin.course-registrations.index')
                ->withErrors(__('This registration has no documents to download.'));
        }

        // Always use a ZIP flow (consistent with bulk download)
        $zip = new \ZipArchive();
        $zipFileName = 'course-registrations-' . now()->format('Ymd-His') . '.zip';
        $tempPath = storage_path('app/tmp-' . uniqid('reg-', false) . '.zip');

        if ($zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('admin.course-registrations.index')->withErrors(__('Unable to prepare download.'));
        }

        $added = 0;
        $folder = Str::slug(($registration->student?->fullname ?? $registration->student?->username ?? 'student') ?: 'student');
        foreach ($paths as $path) {
            $fullPath = Storage::disk('public')->path($path);
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $folder . '/' . basename($path));
                $added++;
            }
        }
        $zip->close();

        if ($added === 0) {
            @unlink($tempPath);
            return redirect()->route('admin.course-registrations.index')->withErrors(__('No files found to include in the archive.'));
        }

        return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function update(UpdateCourseRegistrationRequest $request, CourseRegistration $registration): RedirectResponse
    {
        $validated = $request->validated();
        $status = $validated['status'];
        $comment = $validated['admin_comment'] ?? null;

        $previousStatus = $registration->status;
        $registration->status = $status;
        $registration->admin_comment = $comment;

        $registration->progress_percent = match ($status) {
            'approved' => 100,
            'submitted' => 90,
            'rejected' => 100,
            default => 60,
        };

        if ($status === 'approved') {
            $registration->approved_at = Carbon::now();
        } else {
            $registration->approved_at = null;
        }

        if ($status === 'submitted') {
            $registration->submitted_at = $registration->submitted_at ?? Carbon::now();
        } elseif ($status === 'in_progress') {
            $registration->submitted_at = null;
        }

        if ($status === 'rejected') {
            $registration->pending_documents = max(1, (int) $registration->pending_documents);
        }

        $registration->save();

        $this->notificationService->notifyStatusChange($registration, $previousStatus);

        return redirect()
            ->route('admin.course-registrations.show', $registration)
            ->with('status', __('Registration updated successfully.'));
    }
}
