<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreCourseRegistrationRequest;
use App\Models\CourseRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentCourseRegistrationController extends Controller
{
    public function show(Request $request): View
    {
        $student = $request->user('student');

        $registration = CourseRegistration::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->first();

        return view('dashboards.student.course-registration.show', [
            'title' => 'Course registration',
            'student' => $student,
            'registration' => $registration,
        ]);
    }

    public function store(StoreCourseRegistrationRequest $request): RedirectResponse
    {
        $student = $request->user('student');
        $registration = CourseRegistration::firstOrNew([
            'student_id' => $student->getAuthIdentifier(),
        ]);

        $validated = $request->validated();
        $uploadedPath = $validated['registration_pdf']->store('course-registrations', 'public');

        if ($registration->exists && $registration->document_paths) {
            foreach ($registration->document_paths as $existingPath) {
                Storage::disk('public')->delete($existingPath);
            }
        }

        $registration->fill([
            'status' => 'submitted',
            'progress_percent' => 100,
            'submitted_at' => now(),
            'major_courses' => [],
            'elective_courses' => [],
            'admin_comment' => null,
            'document_paths' => [$uploadedPath],
            'pending_documents' => 1,
        ]);

        $registration->save();

        return redirect()->route('student.course-registration.show')
            ->with('status', __('Your course registration PDF has been uploaded. We will notify you when it is reviewed.'));
    }

    public function destroyDocument(Request $request, CourseRegistration $registration, string $encodedPath): RedirectResponse
    {
        $student = $request->user('student');
        abort_unless($registration->student_id === $student->getAuthIdentifier(), 403);

        $path = base64_decode($encodedPath);
        $documents = $registration->document_paths ?? [];
        $documents = array_filter($documents, fn ($stored) => $stored !== $path);

        Storage::disk('public')->delete($path);

        $registration->document_paths = array_values($documents);
        $registration->pending_documents = count($registration->document_paths);

        if ($registration->pending_documents === 0) {
            $registration->status = 'in_progress';
            $registration->progress_percent = 40;
            $registration->submitted_at = null;
        }

        $registration->save();

        return redirect()->route('student.course-registration.show')
            ->with('status', __('Attachment removed from your registration.'));
    }
}
