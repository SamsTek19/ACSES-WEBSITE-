<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentDashboardService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct(private readonly StudentDashboardService $dashboardService)
    {
    }

    public function __invoke(Request $request): View
    {
        $student = $request->user('student');

        return view('dashboards.student.index', [
            'title' => 'Student Dashboard',
            'hero' => $this->dashboardService->heroData($student),
            'quickActions' => $this->dashboardService->quickActions($student),
            'securityTips' => $this->dashboardService->securityTips(),
            'events' => $this->dashboardService->upcomingEvents(),
            'supportResources' => $this->dashboardService->supportResources($student),
            'calendarWeeks' => $this->dashboardService->calendarMatrix(),
            'calendarMonthLabel' => Carbon::today()->isoFormat('MMMM YYYY'),
            'timelineEntries' => $this->dashboardService->academicTimeline(),
        ]);
    }
}
