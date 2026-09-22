<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentAnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $types = [
            'general' => 'General',
            'security' => 'Security',
            'maintenance' => 'Maintenance',
        ];

        $priorities = [
            'high' => 'High',
            'normal' => 'Normal',
            'low' => 'Low',
        ];

        $typeFilter = $request->query('type');
        $priorityFilter = $request->query('priority');
        $search = trim((string) $request->query('search'));

        $student = $request->user('student');

        $baseQuery = Announcement::query()
            ->published()
            ->forStudent($student);

        if ($typeFilter && array_key_exists($typeFilter, $types)) {
            $baseQuery->where('type', $typeFilter);
        }

        if ($priorityFilter && array_key_exists($priorityFilter, $priorities)) {
            $baseQuery->where('priority', $priorityFilter);
        }

        if ($search !== '') {
            $baseQuery->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $baseQuery->orderByDesc('published_at')->orderByDesc('created_at');

        $announcements = $baseQuery->paginate(9)->withQueryString();

        return view('dashboards.student.announcements.index', [
            'title' => 'Announcements',
            'types' => $types,
            'priorities' => $priorities,
            'filters' => [
                'type' => $typeFilter,
                'priority' => $priorityFilter,
                'search' => $search,
            ],
            'announcements' => $announcements,
        ]);
    }

    public function show(Announcement $announcement): View
    {
        $student = request()->user('student');

        abort_if(! $announcement->published_at || $announcement->published_at->isFuture() || ! $announcement->isVisibleTo($student), 404);

        $related = Announcement::query()
            ->published()
            ->forStudent($student)
            ->where('id', '!=', $announcement->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('dashboards.student.announcements.show', [
            'title' => $announcement->title,
            'announcement' => $announcement,
            'related' => $related,
            'renderedContent' => Str::markdown($announcement->content ?? '', ['html_input' => 'strip']),
        ]);
    }
}
