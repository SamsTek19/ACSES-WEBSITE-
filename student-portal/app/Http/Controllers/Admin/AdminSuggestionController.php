<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkSuggestionActionRequest;
use App\Http\Requests\Admin\UpdateSuggestionStatusRequest;
use App\Models\Suggestion;
use App\Services\Admin\AdminSuggestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSuggestionController extends Controller
{
    public function __construct(private readonly AdminSuggestionService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'status']);
        $perPage = (int) $request->integer('per_page', 20);
        $perPageOptions = [10, 20, 50, 100];
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 20;
        }

        $suggestions = $this->service->suggestions($filters, $perPage);
        $metrics = $this->service->metrics();

        return view('dashboards.admin.suggestions.index', [
            'title' => 'Student suggestions',
            'suggestions' => $suggestions,
            'filters' => $filters,
            'metrics' => $metrics,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
            'categories' => $this->service->categories(),
            'statuses' => $this->service->statuses(),
        ]);
    }

    public function show(Suggestion $suggestion): View
    {
        $suggestion->loadMissing('user:user_id,fullname,username,email');

        return view('dashboards.admin.suggestions.show', [
            'title' => Str::limit($suggestion->subject, 60, '…'),
            'suggestion' => $suggestion,
            'categories' => $this->service->categories(),
            'statuses' => $this->service->statuses(),
        ]);
    }

    public function update(UpdateSuggestionStatusRequest $request, Suggestion $suggestion): RedirectResponse
    {
        $status = SuggestionStatus::from($request->validated('status'));

        $changed = $this->service->updateStatus($suggestion, $status);

        return redirect()
            ->back()
            ->with('status', $changed ? __('Suggestion status updated.') : __('No changes were made.'));
    }

    public function bulk(BulkSuggestionActionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $ids = $data['ids'];
        $statusValue = $data['status'] ?? null;

        $suggestions = Suggestion::query()
            ->with('user:user_id,fullname,username,email')
            ->whereIn('id', $ids)
            ->get();

        $updated = 0;

        if ($data['action'] === 'update_status' && $statusValue) {
            $updated = $this->service->bulkUpdateStatus($suggestions, SuggestionStatus::from($statusValue));
        }

        $redirectUrl = $data['return_url'] ?? null;

        return $redirectUrl
            ? redirect()->to($redirectUrl)->with('status', trans_choice('{0}No suggestions updated.|{1}Suggestion updated.|[2,*]:count suggestions updated.', $updated, ['count' => $updated]))
            : redirect()->route('admin.suggestions.index')
                ->with('status', trans_choice('{0}No suggestions updated.|{1}Suggestion updated.|[2,*]:count suggestions updated.', $updated, ['count' => $updated]));
    }
}
