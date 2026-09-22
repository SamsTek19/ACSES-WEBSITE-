<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Due;
use App\Models\User;
use App\Models\DefaultDueConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\PaymentSetting;

class AdminMaintenanceController extends Controller
{
    public function index(): View
    {
        $orphanedCount = Due::whereDoesntHave('student')->count();
        
        $duplicateSets = Due::select('student_id', 'description', 'academic_year')
            ->groupBy('student_id', 'description', 'academic_year')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $academicYears = Due::distinct()->pluck('academic_year');
        $classes = ['Cyber Security', 'Computer Science', 'Information System'];
        $years = ['1', '2', '3', '4'];
        $allowedDomains = ['st.umat.edu.gh', 'umat.edu.gh', 'gmail.com', 'icloud.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'live.com', 'msn.com'];
        
        $dueTypes = Due::distinct()->pluck('description');
        
        $potentialDummies = User::where('role', 'student')
            ->where(function($q) {
                $q->where('email', 'not like', '%@st.umat.edu.gh')
                  ->where('email', 'not like', '%@umat.edu.gh')
                  ->where('email', 'not like', '%@gmail.com')
                  ->where('email', 'not like', '%@icloud.com')
                  ->where('email', 'not like', '%@outlook.com')
                  ->where('email', 'not like', '%@hotmail.com')
                  ->where('email', 'not like', '%@yahoo.com')
                  ->where('email', 'not like', '%@live.com')
                  ->where('email', 'not like', '%@msn.com');
            })
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return view('dashboards.admin.maint_portal.index', [
            'title' => 'System Maintenance',
            'orphanedCount' => $orphanedCount,
            'duplicateCount' => $duplicateSets->count(),
            'academicYears' => $academicYears,
            'classes' => $classes,
            'years' => $years,
            'dueTypes' => $dueTypes,
            'potentialDummies' => $potentialDummies,
        ]);
    }

    public function deleteOrphaned(): RedirectResponse
    {
        $count = Due::whereDoesntHave('student')->delete();
        return back()->with('status', __("Cleaned up :count orphaned dues.", ['count' => $count]));
    }

    public function resolveDuplicates(): RedirectResponse
    {
        $sets = Due::select('student_id', 'description', 'academic_year')
            ->groupBy('student_id', 'description', 'academic_year')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $deletedCount = 0;
        foreach ($sets as $set) {
            $dues = Due::where('student_id', $set->student_id)
                ->where('description', $set->description)
                ->where('academic_year', $set->academic_year)
                ->orderByRaw("FIELD(payment_status, 'paid', 'pending_verification', 'owing')")
                ->get();

            $keep = $dues->first();
            $extras = $dues->slice(1);
            
            foreach ($extras as $extra) {
                $extra->delete();
                $deletedCount++;
            }
        }

        return back()->with('status', __("Resolved duplicates. Deleted :count extra records.", ['count' => $deletedCount]));
    }

    public function syncMissing(Request $request): RedirectResponse
    {
        $request->validate([
            'academic_year' => 'required',
            'description' => 'required',
        ]);

        $academicYear = $request->academic_year;
        $description = $request->description;

        $template = Due::where('academic_year', $academicYear)
            ->where('description', $description)
            ->first();

        if (!$template) {
            return back()->with('error', __("No template due found for :description (:year).", ['description' => $description, 'year' => $academicYear]));
        }

        $assignedCount = 0;
        User::where('role', 'student')
            ->whereNotNull('email_verified_at')
            ->whereDoesntHave('dues', function($q) use ($academicYear, $description) {
                $q->where('academic_year', $academicYear)->where('description', $description);
            })
            ->chunk(100, function($students) use ($template, &$assignedCount) {
                foreach ($students as $student) {
                    Due::create([
                        'student_id' => $student->user_id,
                        'description' => $template->description,
                        'amount' => $template->amount,
                        'due_date' => $template->due_date,
                        'academic_year' => $template->academic_year,
                        'payment_status' => 'owing',
                        'is_active' => true,
                        'recorded_by' => auth('admin')->id(),
                    ]);
                    $assignedCount++;
                }
            });

        return back()->with('status', __("Assigned missing dues to :count verified students.", ['count' => $assignedCount]));
    }

    public function deleteDummies(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            // Bulk delete all that match the strict suspicious criteria (no gmail/icloud/umat)
            $usersToDelete = User::where('role', 'student')
                ->where(function($q) {
                    $q->where('email', 'not like', '%@st.umat.edu.gh')
                      ->where('email', 'not like', '%@umat.edu.gh')
                      ->where('email', 'not like', '%@gmail.com')
                      ->where('email', 'not like', '%@icloud.com')
                      ->where('email', 'not like', '%@outlook.com')
                      ->where('email', 'not like', '%@hotmail.com')
                      ->where('email', 'not like', '%@yahoo.com')
                      ->where('email', 'not like', '%@live.com')
                      ->where('email', 'not like', '%@msn.com');
                })->get();

            $ids = $usersToDelete->pluck('user_id')->toArray();
        }

        if (empty($ids)) {
            return back()->with('status', __("No dummy accounts found to delete."));
        }

        // Delete associated dues first
        Due::whereIn('student_id', $ids)->delete();
        
        // Delete the users
        $count = User::whereIn('user_id', $ids)->delete();
        
        return back()->with('status', __("Wiped :count fake accounts and all their records.", ['count' => $count]));
    }

    public function updateAmounts(Request $request): RedirectResponse
    {
        $request->validate([
            'class' => 'required',
            'year' => 'nullable',
            'academic_year' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $query = Due::where('academic_year', $request->academic_year)
            ->where('description', $request->description)
            ->where('payment_status', 'owing') // Hard-locked against paid dues
            ->whereHas('student', function($q) use ($request) {
                $q->where('class', $request->class);
                if ($request->year) {
                    $q->where('year', $request->year);
                }
            });

        $count = $query->update(['amount' => $request->amount]);

        return back()->with('status', __("Updated amounts for :count dues.", ['count' => $count]));
    }

    public function mergeDues(Request $request): RedirectResponse
    {
        $request->validate([
            'source_description' => 'required',
            'target_description' => 'required',
            'academic_year' => 'required',
        ]);

        $source = $request->source_description;
        $target = $request->target_description;
        $year = $request->academic_year;

        $dues = Due::where('academic_year', $year)->where('description', $source)->get();
        $mergedCount = 0;

        foreach ($dues as $due) {
            $exists = Due::where('student_id', $due->student_id)
                ->where('academic_year', $year)
                ->where('description', $target)
                ->first();

            if ($exists) {
                if ($due->payment_status === 'paid' && $exists->payment_status !== 'paid') {
                    $exists->delete();
                    $due->description = $target;
                    $due->save();
                } else {
                    $due->delete();
                }
            } else {
                $due->description = $target;
                $due->save();
            }
            $mergedCount++;
        }

        return back()->with('status', __("Merged :count dues from ':source' to ':target'.", ['count' => $mergedCount, 'source' => $source, 'target' => $target]));
    }

    public function optimize(): RedirectResponse
    {
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back()->with('status', __("System optimization completed (caches cleared)."));
    }

    public function migrate(): RedirectResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return back()->with('status', __("Migration completed: ") . $output);
        } catch (\Exception $e) {
            return back()->with('error', __("Migration failed: ") . $e->getMessage());
        }
    }
}
