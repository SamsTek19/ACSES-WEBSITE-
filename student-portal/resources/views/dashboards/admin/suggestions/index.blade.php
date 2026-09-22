@php
    use Illuminate\Support\Str;
    $title = 'Student suggestions';
@endphp

<x-layouts.admin :title="$title">
	<div class="mx-auto w-full max-w-6xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <i class="ri-chat-1-line text-sm" aria-hidden="true"></i>
                    <span>Student feedback</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Suggestions</h1>
                <p class="text-sm text-slate-500">See what students are asking for and track progress as you resolve their feedback.</p>
            </div>
        </header>

        {{-- Metric cards --}}
        <div class="grid gap-4 sm:grid-cols-3 animate-fade-slide animate-fade-slide-delay-200">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <i class="ri-chat-1-line text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ number_format($metrics['total']) }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Total suggestions</p>
                    </div>
                </div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <i class="ri-timer-line text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ number_format($metrics['pending']) }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Awaiting review</p>
                    </div>
                </div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="ri-checkbox-circle-line text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ number_format($metrics['resolvedThisWeek']) }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Resolved this week</p>
                    </div>
                </div>
            </article>
        </div>

        {{-- Main section --}}
        <section class="space-y-0 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-400" x-data="adminSuggestionBulk()" x-init="initialize(@js($suggestions->pluck('id')))" x-cloak>

            {{-- Filter bar --}}
            <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex w-full flex-col gap-1.5 md:w-56">
                        <label for="filter_search" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                        <div class="relative">
                            <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input id="filter_search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Subject, message, student…" class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20" />
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-1.5 md:w-44">
                        <label for="filter_category" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Category</label>
                        <div class="relative">
                            <select id="filter_category" name="category" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                <option value="">All categories</option>
                                @foreach ($categories as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-1.5 md:w-36">
                        <label for="filter_status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                        <div class="relative">
                            <select id="filter_status" name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                <option value="">All statuses</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="flex items-end gap-2 md:ml-auto">
                        <a href="{{ route('admin.suggestions.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Reset</a>
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                            <i class="ri-equalizer-line text-sm"></i>
                            Apply filters
                        </button>
                    </div>
                </form>
            </div>

            {{-- Count + rows-per-page bar --}}
            <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs font-semibold text-slate-600">
                    Showing {{ $suggestions->firstItem() ?? 0 }}–{{ $suggestions->lastItem() ?? 0 }} of {{ $suggestions->total() }} suggestions
                </p>
                <form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="suggestions_per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                    <select id="suggestions_per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" onchange="this.form.submit()">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.suggestions.bulk') }}" x-ref="bulkForm">
                @csrf
                <input type="hidden" name="action" x-ref="actionInput">
                <input type="hidden" name="status" x-ref="statusInput">
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

                {{-- Bulk actions bar --}}
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 md:flex-row md:items-center md:justify-between">
                    <p class="text-xs font-semibold text-slate-600" x-text="bulkSummary"></p>
                    <div class="flex flex-col gap-2 md:flex-row md:items-center" x-show="selectedIds.length" x-cloak x-transition.opacity>
                        <div class="flex items-center gap-2">
                            <label for="suggestion_bulk_status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400 shrink-0">Set status</label>
                            <div class="relative">
                                <select id="suggestion_bulk_status" x-model="statusValue" class="h-8 rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019] md:min-w-[140px]">
                                    <option value="">Select…</option>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            </div>
                        </div>
                        <button type="button" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95 disabled:opacity-60" @click="submit('update_status')" :disabled="!canApplyStatus">
                            <i class="ri-check-double-line text-sm"></i>
                            Apply
                        </button>
                    </div>
                </div>

                {{-- Desktop table --}}
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th scope="col" class="w-12 px-6 py-3">
                                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" @change="toggleAll($event.target.checked)" :checked="allSelected">
                                </th>
                                <th scope="col" class="px-6 py-3">Student</th>
                                <th scope="col" class="px-6 py-3">Category</th>
                                <th scope="col" class="px-6 py-3">Subject</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($suggestions as $suggestion)
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-3.5">
                                        <input type="checkbox" name="ids[]" value="{{ $suggestion->id }}" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" @change="toggle({{ $suggestion->id }}, $event.target.checked)" :checked="selectedIds.includes({{ $suggestion->id }})">
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $suggestion->user?->fullname ?? $suggestion->user?->username ?? 'Unknown student' }}</p>
                                        <p class="text-xs text-slate-400">{{ $suggestion->user?->email }}</p>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-500">{{ $categories[$suggestion->category] ?? Str::headline($suggestion->category) }}</td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $suggestion->subject }}</p>
                                        <p class="mt-0.5 line-clamp-2 text-xs text-slate-400">{{ Str::limit(strip_tags($suggestion->message), 100) }}</p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @php
                                            $sStatus = strtolower($suggestion->status ?? 'pending');
                                            $sBadge = match($sStatus) {
                                                'pending'   => 'bg-amber-50 text-amber-700',
                                                'in_review' => 'bg-blue-50 text-blue-700',
                                                'resolved'  => 'bg-emerald-50 text-emerald-700',
                                                'dismissed' => 'bg-rose-50 text-rose-600',
                                                default     => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $sBadge }}">
                                            {{ $statuses[$suggestion->status] ?? Str::headline($suggestion->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs tabular-nums text-slate-400">{{ $suggestion->created_at?->format('M j, Y · g:i A') ?? '—' }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex justify-end">
                                            <a href="{{ route('admin.suggestions.show', $suggestion) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                                <i class="ri-eye-line"></i>
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                                <i class="ri-chat-off-line text-2xl text-slate-300"></i>
                                            </span>
                                            <p class="text-sm font-semibold text-slate-600">No suggestions found</p>
                                            <p class="text-xs text-slate-400">Adjust filters or encourage students to share their thoughts.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse ($suggestions as $suggestion)
                        <article class="p-4">
                            @php
                                $sStatus = strtolower($suggestion->status ?? 'pending');
                                $sBadge = match($sStatus) {
                                    'pending'   => 'bg-amber-50 text-amber-700',
                                    'in_review' => 'bg-blue-50 text-blue-700',
                                    'resolved'  => 'bg-emerald-50 text-emerald-700',
                                    'dismissed' => 'bg-rose-50 text-rose-600',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $suggestion->subject }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $suggestion->user?->fullname ?? $suggestion->user?->username ?? 'Unknown student' }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $sBadge }}">
                                    {{ $statuses[$suggestion->status] ?? Str::headline($suggestion->status) }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($suggestion->message), 120) }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="ids[]" value="{{ $suggestion->id }}" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" @change="toggle({{ $suggestion->id }}, $event.target.checked)" :checked="selectedIds.includes({{ $suggestion->id }})">
                                    <span class="text-xs text-slate-400">{{ $categories[$suggestion->category] ?? Str::headline($suggestion->category) }}</span>
                                </div>
                                <a href="{{ route('admin.suggestions.show', $suggestion) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                    <i class="ri-eye-line"></i>
                                    View
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center">
                            <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                                <i class="ri-chat-off-line text-2xl text-slate-300"></i>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No suggestions available</p>
                        </div>
                    @endforelse
                </div>
            </form>

            {{-- Pagination --}}
            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs text-slate-400">Page {{ $suggestions->currentPage() }} of {{ $suggestions->lastPage() }}</p>
                <div class="flex justify-center sm:ml-auto sm:justify-end">
                    {{ $suggestions->onEachSide(1)->links('vendor.pagination.data-limit') }}
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
