<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSuggestionRequest;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentSuggestionController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user('student');

        $suggestions = Suggestion::query()
            ->where('user_id', $student->getAuthIdentifier())
            ->latest()
            ->paginate(8);

        $categories = [
            'general' => 'General feedback',
            'academic' => 'Academic support',
            'facilities' => 'Facilities & logistics',
            'technology' => 'Technology & portal',
            'wellness' => 'Counselling & wellness',
            'other' => 'Other',
        ];

        return view('dashboards.student.suggestions.index', [
            'title' => 'Suggestion box',
            'suggestions' => $suggestions,
            'categories' => $categories,
        ]);
    }

    public function store(StoreSuggestionRequest $request): RedirectResponse
    {
        $student = $request->user('student');
        $data = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('suggestions', 'public');
        }

        Suggestion::create([
            'user_id' => $student->getAuthIdentifier(),
            'category' => $data['category'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('student.suggestions.index')
            ->with('status', __('Thanks! Your suggestion has been received. Our team will review it soon.'));
    }
}
