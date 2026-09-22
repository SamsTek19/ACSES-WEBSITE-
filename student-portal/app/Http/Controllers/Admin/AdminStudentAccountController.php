<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\User;
use App\Services\Admin\StudentAccountService;
use App\Services\Admin\AdminDueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AdminStudentAccountController extends Controller
{
    public function __construct(private readonly StudentAccountService $service, private readonly AdminDueService $dues)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'class', 'year']);
        $perPage = (int) $request->integer('per_page', 25);
        $perPageOptions = [25, 50, 100, 250];
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 25;
        }

        $stats = $this->service->stats();
        $filterOptions = $this->service->filterOptions();

        $students = $this->service
            ->studentsQuery($filters)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboards.admin.students.index', [
            'title' => 'Student accounts',
            'students' => $students,
            'stats' => $stats,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        $filterOptions = $this->service->filterOptions();

        return view('dashboards.admin.students.create', [
            'title' => 'New student account',
            'student' => new User(['role' => 'student']),
            'classOptions' => $filterOptions['classes'],
            'yearOptions' => $filterOptions['years'],
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $student = new User();
        $student->fill(Arr::only($data, [
            'username',
            'fullname',
            'email',
            'phone_number',
            'index_number',
            'class',
            'year',
        ]));
        $student->role = 'student';
        $student->is_seller = (bool) ($data['is_seller'] ?? false);
        $student->password = Hash::make($data['password']);
        $student->save();

        $this->dues->syncStudent($student);

        return redirect()
            ->route('admin.students.show', $student)
            ->with('status', __('Student account created successfully.'));
    }

    public function show(User $student): View
    {
        $this->guardStudentRole($student);

        return view('dashboards.admin.students.show', [
            'title' => $student->fullname ? ($student->fullname . ' · Student profile') : 'Student profile',
            'student' => $student,
        ]);
    }

    public function edit(User $student): View
    {
        $this->guardStudentRole($student);

        $filterOptions = $this->service->filterOptions();

        return view('dashboards.admin.students.edit', [
            'title' => 'Edit student account',
            'student' => $student,
            'classOptions' => $filterOptions['classes'],
            'yearOptions' => $filterOptions['years'],
        ]);
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        $this->guardStudentRole($student);

        $data = $request->validated();

        $student->fill(Arr::only($data, [
            'username',
            'fullname',
            'email',
            'phone_number',
            'index_number',
            'class',
            'year',
        ]));
        $student->is_seller = (bool) ($data['is_seller'] ?? false);

        if (! empty($data['password'])) {
            $student->password = Hash::make($data['password']);
        }

        $student->save();

        $this->dues->syncStudent($student);

        return redirect()
            ->route('admin.students.show', $student)
            ->with('status', __('Student account updated successfully.'));
    }

    public function destroy(User $student): RedirectResponse
    {
        $this->guardStudentRole($student);

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('status', __('Student account deleted.'));
    }

    public function export(Request $request): Response
    {
        $filters = $request->only(['search', 'class', 'year']);

        $query = $this->service->studentsQuery($filters);

        return $this->service->exportToExcel($query);
    }

    public function promoteYears(Request $request): RedirectResponse
    {
        $updatedCount = $this->service->promoteAllStudents();

        return redirect()
            ->route('admin.students.index')
            ->with('status', __('Student years updated for :count students.', ['count' => $updatedCount]));
    }

    private function guardStudentRole(User $student): void
    {
        abort_if($student->role !== 'student', 404);
    }
}
