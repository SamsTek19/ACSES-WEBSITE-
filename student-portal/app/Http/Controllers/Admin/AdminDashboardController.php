<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboardService)
    {
    }

    public function __invoke(Request $request): View
    {
        $admin = $request->user('admin');

        return view('dashboards.admin.index', $this->dashboardService->overview($admin) + [
            'title' => 'Admin Dashboard',
        ]);
    }
}
