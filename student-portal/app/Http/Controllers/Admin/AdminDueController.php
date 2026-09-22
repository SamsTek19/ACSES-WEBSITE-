<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDueRequest;
use App\Http\Requests\Admin\UpdateDueRequest;
use App\Models\Due;
use App\Services\Admin\AdminDueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\PaymentSetting;

class AdminDueController extends Controller
{
    public function __construct(private readonly AdminDueService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'academic_year', 'status', 'class', 'year']);
        $perPage = (int) $request->integer('per_page', 25);
        $perPageOptions = [25, 50, 100];

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 25;
        }

        $dues = $this->service->list($filters, $perPage);
        $totals = $this->service->totals($filters);
        $filtersMeta = $this->service->filterOptions();

        $pendingCount = Due::where('payment_status', 'pending_verification')
            ->where('payment_method', 'manual')
            ->count();

        $paymentSettings = [
            'payment_mode' => PaymentSetting::get('payment_mode', 'automated'),
            'manual_bank_name' => PaymentSetting::get('manual_bank_name'),
            'manual_account_name' => PaymentSetting::get('manual_account_name'),
            'manual_account_number' => PaymentSetting::get('manual_account_number'),
            'manual_momo_number' => PaymentSetting::get('manual_momo_number'),
            'manual_momo_name' => PaymentSetting::get('manual_momo_name'),
            'manual_instructions' => PaymentSetting::get('manual_instructions'),
        ];

        return view('dashboards.admin.dues.index', [
            'title' => 'Student dues',
            'dues' => $dues,
            'totals' => $totals,
            'filters' => $filters,
            'filtersMeta' => $filtersMeta,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
            'pendingCount' => $pendingCount,
            'paymentSettings' => $paymentSettings,
        ]);
    }

    public function create(): View
    {
        $filtersMeta = $this->service->filterOptions();
        $matrix = $this->service->matrix();

        return view('dashboards.admin.dues.create', [
            'title' => 'Create academic year due',
            'filtersMeta' => $filtersMeta,
            'matrix' => $matrix,
        ]);
    }

    public function edit(Due $due): View
    {
        return view('dashboards.admin.dues.edit', [
            'title' => 'Edit due',
            'due' => $due,
            'statusOptions' => AdminDueService::STATUS_OPTIONS,
        ]);
    }

    public function store(StoreDueRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $createdCount = $this->service->createDue($payload, $request->user('admin'));

        return redirect()
            ->route('admin.dues.index', ['academic_year' => $payload['academic_year']])
            ->with('status', __('Issued due ":description" for :year to :count students.', [
                'description' => $payload['description'],
                'year' => $payload['academic_year'],
                'count' => number_format($createdCount),
            ]));
    }

    public function update(UpdateDueRequest $request, Due $due): RedirectResponse
    {
        $this->service->updateDue($due, $request->validated(), $request->user('admin'));

        return redirect()
            ->route('admin.dues.index', ['academic_year' => $due->academic_year])
            ->with('status', __('Updated due ":description".', ['description' => $due->description]));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['search', 'academic_year', 'status', 'class', 'year']);

        return $this->service->export($filters);
    }

    public function statistics(Request $request): View
    {
        $filters = $request->only(['search', 'academic_year', 'status', 'class', 'year']);
        $stats = $this->service->statistics($filters);
        $filtersMeta = $this->service->filterOptions();

        return view('dashboards.admin.dues.statistics', [
            'title' => 'Dues performance analytics',
            'stats' => $stats,
            'filters' => $filters,
            'filtersMeta' => $filtersMeta,
        ]);
    }
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_mode' => 'required|in:automated,manual',
            'manual_bank_name' => 'nullable|string|max:100',
            'manual_account_name' => 'nullable|string|max:150',
            'manual_account_number' => 'nullable|string|max:50',
            'manual_momo_number' => 'nullable|string|max:20',
            'manual_momo_name' => 'nullable|string|max:100',
            'manual_instructions' => 'nullable|string|max:1000',
        ]);

        // If switching to automated, we might want to keep the manual details in DB just in case they switch back later.
        // So we won't force-clear them here unless explicitly requested.
        foreach ($data as $key => $value) {
           // If value is null, save empty string or null? The cast (string) $value turns null to "".
           // Let's assume empty string is fine for now.
           PaymentSetting::set($key, (string) $value, null, $request->user()->user_id);
        }

        return back()->with('status', __('Payment settings updated successfully.'));
    }
}
