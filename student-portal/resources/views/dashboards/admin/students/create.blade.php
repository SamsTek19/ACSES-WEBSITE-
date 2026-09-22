@php($title = 'New student account')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.students.index') }}" class="hover:text-[#0b3019] transition-colors">Students</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>New account</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create a student account</h1>
                <p class="text-sm text-slate-500">Provision a new student login with profile details, class placement, and optional marketplace privileges.</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm" aria-hidden="true"></i>
                Back to list
            </a>
        </header>

        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6">
                @csrf

                @include('dashboards.admin.students.partials.form', [
                    'student' => $student,
                    'isEdit' => false,
                    'classOptions' => $classOptions ?? [],
                    'yearOptions' => $yearOptions ?? [],
                ])

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-between">
                    <a href="{{ route('admin.students.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                        <i class="ri-arrow-left-line text-sm" aria-hidden="true"></i>
                        Back to list
                    </a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-check-line text-sm" aria-hidden="true"></i>
                        Create student
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.admin>
