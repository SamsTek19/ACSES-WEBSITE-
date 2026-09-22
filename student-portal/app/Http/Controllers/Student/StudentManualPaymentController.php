<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Due;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentManualPaymentController extends Controller
{
    public function store(Request $request, Due $due): RedirectResponse
    {
        $student = $request->user('student');

        if (! $student || (int)$due->student_id !== (int)$student->getAuthIdentifier()) {
            abort(403);
        }

        if ($due->payment_status === 'paid') {
            return back()->with('status', __('This due is already paid.'));
        }

        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB limit
            'reference' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($due->receipt_path) {
                Storage::disk('public')->delete($due->receipt_path);
            }

            $path = $request->file('receipt')->store('receipts/' . $student->index_number, 'public');
            
            $due->receipt_path = $path;
            $due->payment_method = 'manual';
            $due->payment_status = 'pending_verification';
            $due->payment_reference = $request->input('reference') ?? ('MAN-' . Str::upper(Str::random(8)));
            $due->payment_notes = __('Manual payment proof submitted. Awaiting admin verification.');
            $due->rejection_reason = null; // Clear previous rejection if any
            $due->save();

            return back()->with('status', __('Your payment proof has been submitted. An admin will verify it shortly.'));
        }

        return back()->with('error', __('Failed to upload receipt. Please try again.'));
    }
}
