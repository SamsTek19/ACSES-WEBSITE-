<?php

namespace App\Services\Student;

use App\Models\Due;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentDueService
{
    public const STATUS_LABELS = [
        'owing' => 'Owing',
        'pending_verification' => 'Pending verification',
        'paid' => 'Paid',
    ];

    /**
     * Build summary statistics for the authenticated student's dues.
     */
    public function summary(User $student): array
    {
        $baseQuery = Due::query()
            ->where('student_id', $student->getAuthIdentifier());

        $outstandingQuery = (clone $baseQuery)
            ->whereIn('payment_status', ['owing', 'pending_verification'])
            ->where('is_active', true);

        $paidQuery = (clone $baseQuery)
            ->where('payment_status', 'paid');

        $outstandingAmount = (float) (clone $outstandingQuery)->sum('amount');
        $outstandingCount = (int) (clone $outstandingQuery)->count();

        $paidAmount = (float) (clone $paidQuery)->sum('amount');
        $paidCount = (int) (clone $paidQuery)->count();

        $nextDueModel = (clone $outstandingQuery)
            ->orderBy('due_date')
            ->orderBy('due_id')
            ->first();

        $latestPaymentModel = (clone $paidQuery)
            ->whereNotNull('payment_date')
            ->orderByDesc('payment_date')
            ->orderByDesc('due_id')
            ->first();

        $formatDue = static function (?Due $due): ?array {
            if (! $due) {
                return null;
            }

            return [
                'description' => $due->description,
                'amount' => (float) $due->amount,
                'due_date' => optional($due->due_date)->format('M j, Y'),
                'status' => $due->payment_status,
                'status_label' => self::STATUS_LABELS[$due->payment_status] ?? ucfirst(str_replace('_', ' ', $due->payment_status)),
                'payment_reference' => $due->payment_reference,
                'payment_date' => optional($due->payment_date)->format('M j, Y · g:i A'),
            ];
        };

        return [
            'outstanding_amount' => $outstandingAmount,
            'outstanding_count' => $outstandingCount,
            'paid_amount' => $paidAmount,
            'paid_count' => $paidCount,
            'next_due' => $formatDue($nextDueModel),
            'latest_payment' => $formatDue($latestPaymentModel),
        ];
    }

    /**
     * Paginate dues for the authenticated student with optional filters.
     */
    public function list(User $student, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Due::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->orderByDesc('due_date')
            ->orderByDesc('due_id');

        $status = trim((string) ($filters['status'] ?? ''));
        $academicYear = trim((string) ($filters['academic_year'] ?? ''));
        $search = trim((string) ($filters['search'] ?? ''));

        if ($status !== '' && array_key_exists($status, self::STATUS_LABELS)) {
            $query->where('payment_status', $status);
        }

        if ($academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_reference', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Retrieve filter metadata for the student's dues collection.
     */
    public function filterOptions(User $student): array
    {
        $baseQuery = Due::query()->where('student_id', $student->getAuthIdentifier());

        return [
            'academic_years' => (clone $baseQuery)
                ->select('academic_year')
                ->distinct()
                ->orderByDesc('academic_year')
                ->pluck('academic_year')
                ->all(),
            'statuses' => self::STATUS_LABELS,
        ];
    }
}
