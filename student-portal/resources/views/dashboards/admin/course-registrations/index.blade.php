@php($title = $title ?? 'Manage course registrations')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <i class="ri-file-edit-line text-sm"></i>
                    <span>Course registrations</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Review student submissions</h1>
                <p class="text-sm text-slate-500">Track uploaded PDFs, approve or reject requests, and leave guidance for students.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
                <div class="flex items-center gap-2">
                    <i class="ri-check-double-line text-base text-emerald-600"></i>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 animate-fade-slide">
                <div class="flex items-center gap-2">
                    <i class="ri-error-warning-line text-base text-rose-500"></i>
                    <p>{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <!-- Main section: filters + bulk + table + pagination -->
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200" x-data="courseRegistrationBulk()" x-init="initialize(@js($registrations->pluck('id')))" x-cloak>

            {{-- Filter bar --}}
            <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
                    <input type="hidden" name="per_page" value="{{ request('per_page', $currentPerPage) }}">
                    @foreach (request()->except(['search','status','class','year','page','per_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="sm:col-span-2 lg:col-span-1 flex flex-col gap-1.5">
                        <label for="filter_search" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                        <div class="relative">
                            <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="filter_search" type="search" name="search" value="{{ $search }}" placeholder="Name, email, class" class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-4 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="filter_status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                        <div class="relative">
                            <select id="filter_status" name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                <option value="" @selected($activeStatus === null || $activeStatus === '')>All</option>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($activeStatus === $statusOption)>{{ Str::headline($statusOption) }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="filter_class" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Class</label>
                        <div class="relative">
                            <select id="filter_class" name="class" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                <option value="" @selected($activeClass === null || $activeClass === '')>All</option>
                                @foreach ($classOptions as $option)
                                    <option value="{{ $option }}" @selected($activeClass === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="filter_year" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Year</label>
                        <div class="relative">
                            <select id="filter_year" name="year" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                <option value="" @selected($activeYear === null || $activeYear === '')>All</option>
                                @foreach ($yearOptions as $option)
                                    <option value="{{ $option }}" @selected($activeYear === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="flex items-end gap-2">
                        <a href="{{ route('admin.course-registrations.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Reset</a>
                        <button type="submit" class="h-9 flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                            <i class="ri-filter-3-line text-sm"></i>
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.course-registrations.bulk') }}" x-ref="bulkForm">
                @csrf
                <input type="hidden" name="action" x-ref="actionInput">
                <input type="hidden" name="status" x-ref="statusInput">
                <input type="hidden" name="admin_comment" x-ref="commentInput">
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

                {{-- Bulk actions bar --}}
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs font-semibold text-slate-600" x-text="bulkSummary"></p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3" x-show="selectedIds.length" x-cloak x-transition.opacity>
                        <div class="relative">
                            <select id="bulk_status" x-model="statusValue" class="h-8 appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-[#0b3019] sm:min-w-[160px]">
                                <option value="">Change status</option>
                                @foreach (['in_progress', 'submitted', 'approved', 'rejected'] as $statusOption)
                                    <option value="{{ $statusOption }}">{{ Str::headline($statusOption) }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <div x-show="statusValue === 'rejected'" x-transition>
                            <textarea rows="2" x-model="commentValue" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-[#0b3019] sm:w-56" placeholder="Rejection comment (optional)"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white transition hover:bg-[#072412] disabled:opacity-60 active:scale-95" @click="submit('update_status')" :disabled="!canApplyStatus">
                                <i class="ri-check-double-line text-sm"></i>
                                Apply status
                            </button>
                            <button type="button" class="h-8 inline-flex items-center gap-1.5 rounded-lg border border-[#0b3019]/30 px-3 text-xs font-semibold text-[#0b3019] transition hover:bg-[#0b3019]/8 disabled:opacity-60 active:scale-95" @click="submit('download_documents')" :disabled="!selectedIds.length">
                                <i class="ri-download-2-line text-sm"></i>
                                Download PDFs
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Rows meta --}}
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs font-semibold text-slate-600">Showing {{ $registrations->firstItem() ?? 0 }}–{{ $registrations->lastItem() ?? 0 }} of {{ $registrations->total() }} registrations</p>
                    <form method="GET" id="rows_per_page_form" class="flex items-center justify-center gap-2 sm:justify-end">
                        @foreach (request()->except(['page','per_page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="rows_per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                        <select id="rows_per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" onchange="this.form.submit()">
                            @foreach ($perPageOptions as $opt)
                                <option value="{{ $opt }}" @selected($currentPerPage === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th class="w-12 px-5 py-3">
                                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" @change="toggleAll($event.target.checked)" :checked="allSelected">
                                </th>
                                <th class="px-5 py-3">Student</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Submitted</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($registrations as $registration)
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-5 py-3.5">
                                        <input type="checkbox" name="ids[]" value="{{ $registration->id }}" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" @change="toggle({{ $registration->id }}, $event.target.checked)" :checked="selectedIds.includes({{ $registration->id }})">
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-900">{{ $registration->student?->fullname ?? $registration->student?->username ?? 'Unknown student' }}</p>
                                        <p class="text-xs text-slate-400">{{ $registration->student?->email }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $registration->student?->class }} · Year {{ $registration->student?->year }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ match($registration->status) {
                                            'approved' => 'bg-emerald-50 text-emerald-700',
                                            'rejected' => 'bg-rose-50 text-rose-600',
                                            'submitted' => 'bg-sky-50 text-sky-700',
                                            default => 'bg-amber-50 text-amber-700',
                                        } }}">
                                            <i class="{{ match($registration->status) {
                                                'approved' => 'ri-checkbox-circle-line',
                                                'rejected' => 'ri-close-circle-line',
                                                'submitted' => 'ri-time-line',
                                                default => 'ri-draft-line',
                                            } }} text-sm"></i>
                                            {{ Str::headline($registration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 tabular-nums">
                                        {{ $registration->submitted_at ? $registration->submitted_at->format('M j, Y · g:i A') : '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if (!empty($registration->document_paths))
                                            <a href="{{ route('admin.course-registrations.show', $registration) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                                <i class="ri-download-2-line text-sm"></i>
                                                Download
                                            </a>
                                        @else
                                            <span class="inline-flex cursor-not-allowed items-center gap-1 rounded-md border border-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-300">
                                                <i class="ri-download-2-line text-sm"></i>
                                                Download
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                                <i class="ri-file-forbid-line text-2xl text-slate-300"></i>
                                            </span>
                                            <p class="font-semibold text-slate-600 text-sm">No course registrations found</p>
                                            <p class="text-xs text-slate-400">Adjust filters or check back when students upload their PDFs.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile list --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse ($registrations as $registration)
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $registration->student?->fullname ?? $registration->student?->username ?? 'Unknown student' }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $registration->student?->email }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $registration->student?->class }} · Year {{ $registration->student?->year }}</p>
                                </div>
                                <input type="checkbox" name="ids[]" value="{{ $registration->id }}" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019] shrink-0 mt-0.5" @change="toggle({{ $registration->id }}, $event.target.checked)" :checked="selectedIds.includes({{ $registration->id }})">
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ match($registration->status) {
                                    'approved' => 'bg-emerald-50 text-emerald-700',
                                    'rejected' => 'bg-rose-50 text-rose-600',
                                    'submitted' => 'bg-sky-50 text-sky-700',
                                    default => 'bg-amber-50 text-amber-700',
                                } }}">
                                    {{ Str::headline($registration->status) }}
                                </span>
                                <p class="text-xs text-slate-400 tabular-nums">{{ $registration->submitted_at ? $registration->submitted_at->format('M j, Y') : '—' }}</p>
                            </div>
                            <div class="mt-3">
                                @if (!empty($registration->document_paths))
                                    <a href="{{ route('admin.course-registrations.show', $registration) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                        <i class="ri-download-2-line"></i>
                                        Download
                                    </a>
                                @else
                                    <span class="inline-flex cursor-not-allowed items-center gap-1 rounded-md border border-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-300">
                                        <i class="ri-download-2-line"></i>
                                        Download
                                    </span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center">
                            <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                                <i class="ri-file-forbid-line text-2xl text-slate-300"></i>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No course registrations found</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs text-slate-400">Page {{ $registrations->currentPage() }} of {{ $registrations->lastPage() }}</p>
                    <div class="flex justify-center sm:ml-auto sm:justify-end">
                        {{ $registrations->onEachSide(1)->links('vendor.pagination.data-limit') }}
                    </div>
                </div>

            </form>
        </section>
    </div>
</x-layouts.admin>
