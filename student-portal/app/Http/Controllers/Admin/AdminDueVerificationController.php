<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Due;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AdminDueVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Due::with('student')
            ->where('payment_status', 'pending_verification')
            ->where('payment_method', 'manual')
            ->orderBy('updated_at', 'asc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('index_number', 'like', "%{$search}%");
            });
        }

        $pendingDues = $query->paginate(20);

        return view('dashboards.admin.dues.verifications', [
            'title' => 'Pending Dues Verifications',
            'pendingDues' => $pendingDues,
        ]);
    }

    public function approve(Due $due, Request $request): RedirectResponse
    {
        $due->payment_status = 'paid';
        $due->verification_date = now();
        $due->verified_by = $request->user()->user_id;
        $due->verification_notes = 'Approved by ' . $request->user()->username;
        $due->save();

        return back()->with('status', __('Due payment for :name has been approved.', ['name' => $due->student->fullname]));
    }

    public function reject(Due $due, Request $request): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        // Delete the receipt if rejected? Maybe keep it for record? User asked to reject with reason.
        // We set status back to 'owing' so they can try again.
        
        $due->payment_status = 'owing';
        $due->rejection_reason = $request->input('rejection_reason');
        $due->payment_notes = 'Rejected: ' . $request->input('rejection_reason');
        $due->save();

        return back()->with('status', __('Due payment for :name has been rejected.', ['name' => $due->student->fullname]));
    }
}
