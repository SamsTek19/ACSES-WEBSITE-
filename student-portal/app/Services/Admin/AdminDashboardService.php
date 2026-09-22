<?php

namespace App\Services\Admin;

use App\Models\CourseRegistration;
use App\Models\Due;
use App\Models\Event;
use App\Models\Suggestion;
use App\Models\SupportResource;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminDashboardService
{
    public function overview(?User $admin): array
    {
        $adminName = $admin?->fullname ?? $admin?->username ?? 'Administrator';
        $greeting = $this->greetingForNow();
        $now = Carbon::now();

        $studentCount = User::query()
            ->where('role', 'student')
            ->count();

        $outstandingDuesQuery = Due::query()->outstanding();
        $outstandingDueCount = (clone $outstandingDuesQuery)->count();
        $outstandingDueAmount = (float) (clone $outstandingDuesQuery)->sum('amount');

        $registrationQuery = CourseRegistration::query();
        $pendingRegistrations = (clone $registrationQuery)
            ->whereIn('status', ['in_progress', 'submitted'])
            ->count();
        $submittedToday = (clone $registrationQuery)
            ->whereDate('submitted_at', $now->toDateString())
            ->count();
        $approvedThisWeek = (clone $registrationQuery)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $now->copy()->startOfWeek())
            ->count();

        $suggestionsPending = Suggestion::query()
            ->where('status', 'pending')
            ->count();
        $suggestionsResolvedWeek = Suggestion::query()
            ->where('status', 'resolved')
            ->where('handled_at', '>=', $now->copy()->startOfWeek())
            ->count();

        $overviewCards = [
            [
                'label' => 'Students onboarded',
                'value' => number_format($studentCount),
                'description' => 'Active student accounts on the platform.',
                'icon' => 'ri-user-star-fill',
                'link' => route('admin.students.index'),
                'cta' => 'Manage students',
            ],
            [
                'label' => 'Outstanding dues',
                'value' => 'GHS ' . number_format($outstandingDueAmount, 2),
                'description' => $outstandingDueCount . ' invoices pending payment.',
                'icon' => 'ri-money-dollar-circle-fill',
                'link' => route('admin.dues.index'),
                'cta' => 'Review dues',
            ],
            [
                'label' => 'Pending registrations',
                'value' => number_format($pendingRegistrations),
                'description' => $approvedThisWeek . ' approved this week.',
                'icon' => 'ri-task-fill',
                'link' => route('admin.course-registrations.index'),
                'cta' => 'See registrations',
            ],
            [
                'label' => 'Suggestions awaiting review',
                'value' => number_format($suggestionsPending),
                'description' => $suggestionsResolvedWeek . ' resolved this week.',
                'icon' => 'ri-chat-smile-3-fill',
                'link' => route('admin.suggestions.index'),
                'cta' => 'Open suggestions',
            ],
        ];

        $upcomingEvents = Event::query()
            ->upcoming()
            ->limit(3)
            ->get()
            ->map(function (Event $event): array {
                return [
                    'title' => $event->title,
                    'schedule' => optional($event->start_at)->isoFormat('MMM D · h:mm A'),
                    'category' => Str::headline($event->category ?? 'General'),
                    'location' => $event->location,
                ];
            });

        $recentSuggestions = Suggestion::query()
            ->with(['user:user_id,fullname,username,email'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function (Suggestion $suggestion): array {
                return [
                    'subject' => $suggestion->subject,
                    'category' => Str::headline($suggestion->category ?? 'General'),
                    'status' => Str::headline($suggestion->status ?? 'Pending'),
                    'submitted_at' => optional($suggestion->created_at)->diffForHumans(),
                    'owner' => $suggestion->user?->fullname
                        ?? $suggestion->user?->username
                        ?? 'Student',
                ];
            });

        // Dues status breakdown for charts
        $duesBreakdown = Due::query()
            ->where('is_active', true)
            ->selectRaw("payment_status, SUM(amount) as total_amount")
            ->groupBy('payment_status')
            ->pluck('total_amount', 'payment_status')
            ->toArray();

        $chartPaidAmount = (float) ($duesBreakdown['paid'] ?? 0);
        $chartPendingAmount = (float) ($duesBreakdown['pending_verification'] ?? 0);
        $chartOwingAmount = (float) ($duesBreakdown['owing'] ?? 0);
        $totalDues = $chartPaidAmount + $chartPendingAmount + $chartOwingAmount;
        $chartCollectionRate = $totalDues > 0 ? ($chartPaidAmount / $totalDues) * 100 : 0;

        // Student counts grouped by class/programme
        $classDistribution = User::query()
            ->where('role', 'student')
            ->selectRaw("class, COUNT(*) as count")
            ->groupBy('class')
            ->orderByDesc('count')
            ->get();

        $classLabels = $classDistribution->pluck('class')->map(fn($c) => $c ?? 'Unassigned')->toArray();
        $classData = $classDistribution->pluck('count')->toArray();

        return [
            'adminName' => $adminName,
            'hero' => [
                'greeting' => $greeting,
                'message' => 'Monitor student activity, approvals, and support trends from a single view.',
                'lastUpdated' => $now->isoFormat('MMMM D, YYYY [at] h:mm A'),
            ],
            'overviewCards' => $overviewCards,
            'registrationSummary' => [
                'pending' => $pendingRegistrations,
                'submittedToday' => $submittedToday,
                'approvedThisWeek' => $approvedThisWeek,
            ],
            'dueSummary' => [
                'count' => $outstandingDueCount,
                'amount' => $outstandingDueAmount,
            ],
            'resourcesTotal' => SupportResource::query()->count(),
            'upcomingEvents' => $upcomingEvents,
            'recentSuggestions' => $recentSuggestions,
            'chartsData' => [
                'dues' => [
                    'labels' => ['Paid', 'Pending Verification', 'Outstanding'],
                    'data' => [$chartPaidAmount, $chartPendingAmount, $chartOwingAmount],
                    'collectionRate' => round($chartCollectionRate, 1),
                ],
                'students' => [
                    'labels' => $classLabels,
                    'data' => $classData,
                ],
            ],
        ];
    }

    private function greetingForNow(): string
    {
        $now = Carbon::now();
        $hour = (int) $now->format('H');

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }
}
