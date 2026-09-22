<?php

namespace App\Services\Admin;

use App\Enums\SuggestionStatus;
use App\Models\Suggestion;
use App\Notifications\StudentSuggestionStatusUpdatedNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminSuggestionService
{
    private const CATEGORY_LABELS = [
        'general' => 'General feedback',
        'academic' => 'Academic support',
        'facilities' => 'Facilities & logistics',
        'technology' => 'Technology & portal',
        'wellness' => 'Counselling & wellness',
        'other' => 'Other',
    ];

    /**
     * Build suggestion list for the admin index view.
     */
    public function suggestions(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Suggestion::query()
            ->with(['user:user_id,fullname,username,email'])
            ->latest();

        if ($search = trim((string) Arr::get($filters, 'search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($relation) use ($search) {
                        $relation->where('fullname', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($category = Arr::get($filters, 'category')) {
            $query->where('category', $category);
        }

        if (($status = Arr::get($filters, 'status')) !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Provide quick metrics for the suggestions dashboard.
     */
    public function metrics(): array
    {
        $total = Suggestion::query()->count();
        $pending = Suggestion::query()->where('status', 'pending')->count();
        $resolvedThisWeek = Suggestion::query()
            ->where('status', 'resolved')
            ->where('handled_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'resolvedThisWeek' => $resolvedThisWeek,
        ];
    }

    public function categories(): array
    {
        return self::CATEGORY_LABELS;
    }

    public function statuses(): array
    {
        return SuggestionStatus::labels();
    }

    public function updateStatus(Suggestion $suggestion, SuggestionStatus $status): bool
    {
        $previous = $this->resolveStatus($suggestion->status);

        if ($previous === $status) {
            return false;
        }

        $suggestion->status = $status->value;
        $suggestion->handled_at = $status->marksHandled() ? Carbon::now() : null;
        $suggestion->save();

        $this->notifyStudent($suggestion, $previous);

        return true;
    }

    public function bulkUpdateStatus(Collection $suggestions, SuggestionStatus $status): int
    {
        $updated = 0;

        foreach ($suggestions as $suggestion) {
            if (! $suggestion instanceof Suggestion) {
                continue;
            }

            $previous = $this->resolveStatus($suggestion->status);

            if ($previous === $status) {
                continue;
            }

            $suggestion->status = $status->value;
            $suggestion->handled_at = $status->marksHandled() ? Carbon::now() : null;
            $suggestion->save();

            $this->notifyStudent($suggestion, $previous);
            $updated++;
        }

        return $updated;
    }

    private function resolveStatus(?string $status): SuggestionStatus
    {
        return match ($status) {
            SuggestionStatus::InReview->value => SuggestionStatus::InReview,
            SuggestionStatus::Resolved->value => SuggestionStatus::Resolved,
            SuggestionStatus::Dismissed->value => SuggestionStatus::Dismissed,
            default => SuggestionStatus::Pending,
        };
    }

    private function notifyStudent(Suggestion $suggestion, SuggestionStatus $previous): void
    {
        $student = $suggestion->user;

        if (! $student || empty($student->email)) {
            return;
        }

        $student->notify(new StudentSuggestionStatusUpdatedNotification($suggestion, $previous));
    }
}
