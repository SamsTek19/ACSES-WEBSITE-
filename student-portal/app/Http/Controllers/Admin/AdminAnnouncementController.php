<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use App\Services\Admin\AdminAnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function __construct(private readonly AdminAnnouncementService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'type', 'priority', 'target_type']);
        $perPage = (int) $request->integer('per_page', 15);
        $perPageOptions = [10, 15, 25, 50];

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $announcements = $this->service->list($filters, $perPage);

        return view('dashboards.admin.announcements.index', [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'filters' => $filters,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
            'types' => AdminAnnouncementService::TYPES,
            'priorities' => AdminAnnouncementService::PRIORITIES,
            'targetTypes' => AdminAnnouncementService::TARGET_TYPES,
        ]);
    }

    public function create(): View
    {
        return view('dashboards.admin.announcements.create', [
            'title' => 'Send announcement',
            'types' => AdminAnnouncementService::TYPES,
            'priorities' => AdminAnnouncementService::PRIORITIES,
            'targetTypes' => AdminAnnouncementService::TARGET_TYPES,
            'options' => $this->service->targetOptions(),
        ]);
    }

    public function edit(Announcement $announcement): View
    {
        $options = $this->service->targetOptions();
        $targetFilters = $announcement->target_filters ?? [];

        $selectedStudents = $targetFilters['students'] ?? [];

        if (! empty($selectedStudents)) {
            $studentLabels = User::query()
                ->whereIn('user_id', $selectedStudents)
                ->get(['user_id', 'fullname', 'username', 'email'])
                ->mapWithKeys(function (User $student) {
                    $label = $student->fullname
                        ? $student->fullname . ($student->email ? " ({$student->email})" : '')
                        : ($student->username ?? $student->email ?? ('ID ' . $student->user_id));

                    return [$student->user_id => $label];
                })->all();

            $options['students'] = $studentLabels + $options['students'];
        }

        return view('dashboards.admin.announcements.edit', [
            'title' => 'Edit announcement',
            'announcement' => $announcement,
            'types' => AdminAnnouncementService::TYPES,
            'priorities' => AdminAnnouncementService::PRIORITIES,
            'targetTypes' => AdminAnnouncementService::TARGET_TYPES,
            'options' => $options,
            'targetFilters' => $targetFilters,
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $announcement = $this->service->create($request->validated(), $request->user('admin'));

        return redirect()->route('admin.announcements.index')
            ->with('status', __('Announcement ":title" sent to students.', ['title' => $announcement->title]));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->service->update($announcement, $request->validated());

        return redirect()->route('admin.announcements.index')
            ->with('status', __('Announcement ":title" updated.', ['title' => $announcement->title]));
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->service->delete($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('status', __('Announcement ":title" deleted.', ['title' => $announcement->title]));
    }
}
