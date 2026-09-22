<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademicTimelineEntryRequest;
use App\Http\Requests\Admin\UpdateAcademicTimelineEntryRequest;
use App\Models\AcademicTimelineEntry;
use App\Services\AcademicTimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAcademicTimelineController extends Controller
{
    public function __construct(private readonly AcademicTimelineService $timelineService)
    {
    }

    public function index(Request $request): View
    {
        $perPageOptions = [10, 25, 50, 100];
        $perPage = (int) $request->integer('per_page', 10);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $entries = $this->timelineService->paginateEntries($perPage);

        return view('dashboards.admin.timeline.index', [
            'title' => 'Academic timeline',
            'entries' => $entries,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('dashboards.admin.timeline.create', [
            'title' => 'Create timeline entry',
            'entry' => new AcademicTimelineEntry(),
        ]);
    }

    public function store(StoreAcademicTimelineEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published', true);

        $this->timelineService->create($data);

        return redirect()
            ->route('admin.timeline.index')
            ->with('status', __('Timeline entry created successfully.'));
    }

    public function edit(AcademicTimelineEntry $timeline): View
    {
        return view('dashboards.admin.timeline.edit', [
            'title' => 'Edit timeline entry',
            'entry' => $timeline,
        ]);
    }

    public function update(UpdateAcademicTimelineEntryRequest $request, AcademicTimelineEntry $timeline): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published', false);

        $this->timelineService->update($timeline, $data);

        return redirect()
            ->route('admin.timeline.index')
            ->with('status', __('Timeline entry updated successfully.'));
    }

    public function destroy(AcademicTimelineEntry $timeline): RedirectResponse
    {
        $this->timelineService->delete($timeline);

        return redirect()
            ->route('admin.timeline.index')
            ->with('status', __('Timeline entry removed.'));
    }
}
