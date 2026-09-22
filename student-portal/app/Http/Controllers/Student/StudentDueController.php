<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentDueService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\PaymentSetting;

class StudentDueController extends Controller
{
    public function __construct(private readonly StudentDueService $dueService)
    {
    }

    public function index(Request $request): View
    {
        $student = $request->user('student');

        $filters = $request->only(['status', 'academic_year', 'search']);
        $perPage = (int) $request->integer('per_page', 10);
        $perPageOptions = [10, 25, 50];

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $summary = $this->dueService->summary($student);
        $dues = $this->dueService->list($student, $filters, $perPage);
        $filterOptions = $this->dueService->filterOptions($student);

        return view('dashboards.student.dues.index', [
            'title' => 'My dues',
            'summary' => $summary,
            'dues' => $dues,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
            'statusLabels' => StudentDueService::STATUS_LABELS,
            'paymentSettings' => [
                'mode' => PaymentSetting::get('payment_mode', 'automated'),
                'bank_name' => PaymentSetting::get('manual_bank_name'),
                'account_name' => PaymentSetting::get('manual_account_name'),
                'account_number' => PaymentSetting::get('manual_account_number'),
                'momo_number' => PaymentSetting::get('manual_momo_number'),
                'momo_name' => PaymentSetting::get('manual_momo_name'),
                'instructions' => PaymentSetting::get('manual_instructions'),
            ],
        ]);
    }
}
