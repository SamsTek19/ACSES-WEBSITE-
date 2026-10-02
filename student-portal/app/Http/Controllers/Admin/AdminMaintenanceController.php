<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Due;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminMaintenanceController extends Controller
{
    public function index(): View
    {
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
            'potentialDummies' => $potentialDummies,
        ]);
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

        // Remove linked records before deleting the accounts.
        Due::whereIn('student_id', $ids)->delete();
        
        // Delete the users
        $count = User::whereIn('user_id', $ids)->delete();
        
        return back()->with('status', __("Wiped :count fake accounts and all their records.", ['count' => $count]));
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
