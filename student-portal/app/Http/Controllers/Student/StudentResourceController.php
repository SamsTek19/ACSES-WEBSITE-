<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentResourceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $student = $request->user('student');

        $allResources = $this->loadResources($student);

        $resources = $allResources
            ->filter(function (array $resource) use ($search) {
                $matchesSearch = $search === ''
                    || Str::contains(Str::lower($resource['title']), Str::lower($search))
                    || Str::contains(Str::lower($resource['description']), Str::lower($search));

                return $matchesSearch;
            })
            ->values();

        return view('dashboards.student.resources.index', [
            'title' => 'Academic resources',
            'resources' => $resources,
            'search' => $search,
            'totalResources' => $resources->count(),
        ]);
    }

    private function loadResources(?\App\Models\User $student): Collection
    {
        $class = $student?->class ?: null;
        $year = $student?->year !== null ? (string) $student->year : null;

        $databaseResources = SupportResource::query()
            ->forAudience($class, $year)
            ->ordered()
            ->get()
            ->map(function (SupportResource $resource) {
                $isFile = $resource->is_file;

                $ctaLabel = $resource->cta_label;
                if (! $ctaLabel) {
                    $ctaLabel = $isFile ? __('Download resource') : __('Open resource');
                }

                $url = $isFile
                    ? $resource->download_url
                    : ($resource->cta_url ?? '#');

                $openInNewTab = $isFile || ($url && Str::startsWith($url, ['http://', 'https://']));

                $badgeIcon = $resource->icon ?? ($isFile ? 'fa-file-lines' : 'fa-book-open-reader');

                return [
                    'title' => $resource->title,
                    'description' => $resource->description,
                    'cta_label' => $ctaLabel,
                    'cta_url' => $url,
                    'badge_label' => Str::headline($resource->content_type ?? 'Resource'),
                    'badge_icon' => $badgeIcon,
                    'resource_type' => $resource->resource_type,
                    'is_file' => $isFile,
                    'open_in_new_tab' => $openInNewTab,
                ];
            });

        return $databaseResources;
    }
}
