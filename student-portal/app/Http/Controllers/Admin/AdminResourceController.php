<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupportResourceRequest;
use App\Http\Requests\Admin\UpdateSupportResourceRequest;
use App\Models\SupportResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminResourceController extends Controller
{
    public function index(Request $request): View
    {
        $perPageOptions = [10, 25, 50];
        $perPage = (int) $request->integer('per_page', 10);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $resources = SupportResource::query()
            ->ordered()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('dashboards.admin.resources.index2', [
            'title' => 'Academic resources',
            'resources' => $resources,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('dashboards.admin.resources.create', [
            'title' => 'Add academic resource',
            'resource' => new SupportResource([
                'resource_type' => SupportResource::RESOURCE_TYPES[0],
                'content_type' => SupportResource::CONTENT_TYPES[0],
                'visibility' => 'student',
            ]),
            'resourceTypes' => SupportResource::RESOURCE_TYPES,
            'contentTypes' => SupportResource::CONTENT_TYPES,
            'classOptions' => SupportResource::CLASSES,
            'yearOptions' => SupportResource::YEARS,
        ]);
    }

    public function store(StoreSupportResourceRequest $request): RedirectResponse
    {
        $data = $this->preparePayload($request->validated());

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('resources', 'public');
            $data['resource_type'] = 'file';
        }

        SupportResource::create($data);

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Academic resource created.'));
    }

    public function edit(SupportResource $resource): View
    {
        return view('dashboards.admin.resources.edit', [
            'title' => 'Edit academic resource',
            'resource' => $resource,
            'resourceTypes' => SupportResource::RESOURCE_TYPES,
            'contentTypes' => SupportResource::CONTENT_TYPES,
            'classOptions' => SupportResource::CLASSES,
            'yearOptions' => SupportResource::YEARS,
        ]);
    }

    public function update(UpdateSupportResourceRequest $request, SupportResource $resource): RedirectResponse
    {
        $data = $this->preparePayload($request->validated());

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = $request->file('file')->store('resources', 'public');
            $data['resource_type'] = 'file';
        }

        if (($data['resource_type'] ?? $resource->resource_type) !== 'file' && $resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
            $data['file_path'] = null;
        }

        $resource->update($data);

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Academic resource updated.'));
    }

    public function destroy(SupportResource $resource): RedirectResponse
    {
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()
            ->route('admin.resources.index')
            ->with('status', __('Academic resource removed.'));
    }

    private function preparePayload(array $data): array
    {
        $data['target_classes'] = $this->normalizeAudience($data['target_classes'] ?? null);
        $data['target_years'] = $this->normalizeAudience($data['target_years'] ?? null);

        if (($data['resource_type'] ?? null) === 'file') {
            $data['cta_url'] = null;
            $data['cta_label'] = $data['cta_label'] ?? __('Download resource');
        }

        return $data;
    }

    private function normalizeAudience($value): ?array
    {
        if (is_array($value)) {
            $filtered = array_values(array_filter($value, fn ($item) => filled($item)));

            return $filtered === [] ? null : $filtered;
        }

        return null;
    }
}
