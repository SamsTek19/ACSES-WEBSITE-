@php($title = 'Student Accounts')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl px-5 py-8 sm:px-6 lg:px-8">
        <div class="space-y-6">

            <!-- Header -->
            <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <i class="ri-team-line text-sm"></i>
                        <span>Student accounts</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manage student database</h1>
                    <p class="text-sm text-slate-500">Review onboarded student profiles, promote academic years, and manage credentials.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('admin.students.export', request()->query()) }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                        <i class="ri-download-2-line text-sm"></i>
                        Export
                    </a>
                    <form method="POST" action="{{ route('admin.students.promote-years') }}" onsubmit="return confirm('Promote all students to the next academic year?');">
                        @csrf
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-700 transition hover:bg-amber-100 active:scale-95">
                            <i class="ri-arrow-up-circle-line text-sm"></i>
                            Promote years
                        </button>
                    </form>
                    <a href="{{ route('admin.students.create') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-user-add-line text-sm"></i>
                        New student
                    </a>
                </div>
            </header>

            <!-- Stats Summary Grid -->
            <section class="grid gap-4 lg:grid-cols-4 animate-fade-slide animate-fade-slide-delay-200">

                <article class="rounded-2xl border border-[#0b3019]/25 bg-gradient-to-br from-[#0b3019] to-[#072412] p-5 text-white shadow-md flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">Total enrollment</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                            <i class="ri-group-line text-lg"></i>
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-extrabold tracking-tight tabular-nums">{{ number_format($stats['total']) }}</p>
                        <p class="mt-1 text-[10px] text-emerald-100/70 font-semibold uppercase tracking-wider">Active profiles</p>
                    </div>
                </article>

                <div class="lg:col-span-3 grid gap-4 sm:grid-cols-3">
                    @forelse ($stats['class_breakdown'] as $classStat)
                        <article class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm flex flex-col justify-between">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-xs font-bold text-[#0b3019]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#0b3019]/30"></span>
                                    <span>{{ $classStat['name'] }}</span>
                                </div>
                                <p class="text-xl font-extrabold text-slate-800 tracking-tight tabular-nums">{{ number_format($classStat['total']) }}</p>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-1.5 border-t border-slate-100 pt-2.5 text-[10px]">
                                @foreach ($stats['year_buckets'] as $year)
                                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-2 py-1 border border-slate-100">
                                        <dt class="font-bold text-slate-400">Yr {{ $year }}</dt>
                                        <dd class="font-extrabold text-slate-800 tabular-nums">{{ number_format($classStat['years'][$year] ?? 0) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </article>
                    @empty
                        <div class="sm:col-span-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-6 text-center text-xs text-slate-400">
                            No onboarded classes found.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Graduates -->
            <section class="rounded-2xl border border-slate-100 bg-slate-50/30 p-5 animate-fade-slide animate-fade-slide-delay-200">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-2">
                            <i class="ri-graduation-cap-line text-[#0b3019]"></i>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0b3019]">Alumni &amp; Graduates</h3>
                        </div>
                        <span class="text-xs font-bold text-slate-800 bg-[#0b3019]/5 px-2.5 py-0.5 rounded-md tabular-nums">{{ number_format($stats['graduated_total'] ?? 0) }} total</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse (($stats['graduated_class_breakdown'] ?? []) as $gradStat)
                            <article class="rounded-xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex items-center justify-between gap-3">
                                <div class="space-y-0.5 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate">{{ $gradStat['name'] }}</p>
                                    <p class="text-base font-extrabold text-[#0b3019] tabular-nums">{{ number_format($gradStat['total'] ?? 0) }} graduates</p>
                                </div>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#0b3019]/5 text-[#0b3019] shrink-0">
                                    <i class="ri-medal-line"></i>
                                </span>
                            </article>
                        @empty
                            <p class="text-xs text-slate-400">No graduates recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Filters + Table + Pagination -->
            <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-400">

                {{-- Filter bar --}}
                <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                    <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 items-end">
                        @foreach (request()->except(['search', 'class', 'year', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="flex flex-col gap-1.5">
                            <label for="filter_search" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                            <div class="relative">
                                <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="filter_search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, index, email..." class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-4 text-xs text-slate-800 placeholder-slate-400 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition">
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="filter_class" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Class</label>
                            <div class="relative">
                                <select id="filter_class" name="class" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-10 text-xs text-slate-800 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition">
                                    <option value="">All classes</option>
                                    @foreach ($filterOptions['classes'] as $classOption)
                                        <option value="{{ $classOption }}" @selected(($filters['class'] ?? '') === $classOption)>{{ $classOption }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="filter_year" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Year</label>
                            <div class="relative">
                                <select id="filter_year" name="year" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-10 text-xs text-slate-800 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition">
                                    <option value="">All years</option>
                                    @foreach ($filterOptions['years'] as $yearOption)
                                        <option value="{{ $yearOption }}" @selected(($filters['year'] ?? '') == $yearOption)>Year {{ $yearOption }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="flex items-end gap-2">
                            <a href="{{ route('admin.students.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Reset</a>
                            <button type="submit" class="h-9 flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                                <i class="ri-filter-3-line text-sm"></i>
                                Apply filters
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Rows meta --}}
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs font-semibold text-slate-600">Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students</p>
                    <form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
                        @foreach (request()->except(['per_page', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="students_per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                        <select id="students_per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" x-on:change="$el.form.submit()">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Desktop table --}}
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs text-slate-600">
                        <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Student profile</th>
                                <th class="px-5 py-3">Programme · Cohort</th>
                                <th class="px-5 py-3">Created</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($students as $student)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b3019]/8 text-xs font-bold text-[#0b3019] shrink-0 border border-[#0b3019]/10">
                                                {{ Str::of($student->fullname ?? $student->username)->trim()->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->take(2)->implode('') }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-800 text-sm truncate leading-snug">{{ $student->fullname ?? $student->username }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5 truncate leading-none">{{ $student->email }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1 leading-none font-semibold">{{ $student->username }}@if($student->index_number) · {{ $student->index_number }}@endif</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="space-y-1">
                                            <p class="font-bold text-slate-700">{{ $student->class ?? '—' }}</p>
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-500 uppercase tracking-wider">{{ $student->year ? 'Year ' . $student->year : '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-[11px] text-slate-400 font-semibold">{{ $student->created_at?->format('M j, Y · g:i A') ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-[#0b3019]/40 hover:text-[#0b3019] transition active:scale-95" title="View profile">
                                                <i class="ri-eye-line text-sm"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-[#0b3019]/40 hover:text-[#0b3019] transition active:scale-95" title="Edit account">
                                                <i class="ri-pencil-line text-sm"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline-flex" onsubmit="return confirm('Delete this student account? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50 transition active:scale-95" title="Delete account">
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                                <i class="ri-group-line text-2xl text-slate-300"></i>
                                            </span>
                                            <p class="font-bold text-slate-600 text-sm">No student accounts found</p>
                                            <p class="text-xs text-slate-400">Try adjusting your filters or invite new students.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile list --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse ($students as $student)
                        <article class="p-4">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b3019]/8 text-xs font-bold text-[#0b3019] shrink-0 border border-[#0b3019]/10">
                                    {{ Str::of($student->fullname ?? $student->username)->trim()->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->take(2)->implode('') }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-800 truncate leading-snug">{{ $student->fullname ?? $student->username }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $student->email }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-500">{{ $student->year ? 'Yr ' . $student->year : '—' }}</span>
                            </div>
                            <dl class="mt-2.5 grid grid-cols-2 gap-y-1.5 text-xs text-slate-500">
                                <div><dt class="text-slate-400">Programme</dt><dd class="font-semibold text-slate-700">{{ $student->class ?? '—' }}</dd></div>
                                <div><dt class="text-slate-400">Username</dt><dd class="font-semibold">{{ $student->username }}</dd></div>
                            </dl>
                            <div class="mt-3 flex items-center gap-2">
                                <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                    <i class="ri-eye-line"></i>
                                    View
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                    <i class="ri-pencil-line"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline-flex" onsubmit="return confirm('Delete this student account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                        <i class="ri-delete-bin-line"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center">
                            <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                                <i class="ri-group-line text-2xl text-slate-300"></i>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No student accounts found</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs text-slate-400">Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</p>
                    <div class="flex justify-center sm:ml-auto sm:justify-end">
                        {{ $students->onEachSide(1)->links('vendor.pagination.data-limit') }}
                    </div>
                </div>

            </section>

        </div>
    </div>
</x-layouts.admin>
