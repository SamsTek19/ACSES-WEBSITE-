<x-layouts.dashboard :title="$title">
@include('components.dashboard.skeleton-styles')

@php
    $hasOutstanding   = ($summary['outstanding_amount'] ?? 0) > 0;
    $outstandingCount = $summary['outstanding_count'] ?? 0;
    $paidCount        = $summary['paid_count'] ?? 0;
    $nextDue          = $summary['next_due'] ?? null;
    $latestPayment    = $summary['latest_payment'] ?? null;

    $activeStatus = $filters['status'] ?? '';
    $activeYear   = $filters['academic_year'] ?? '';
    $searchTerm   = $filters['search'] ?? '';
    $isFiltered   = $activeStatus !== '' || $activeYear !== '' || $searchTerm !== '';

    $statusColors = [
        'owing'                => 'bg-emerald-900/10 text-[#0b3019] ring-1 ring-inset ring-emerald-900/20',
        'pending_verification' => 'bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200',
        'paid'                 => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
    ];
    $statusIcons = [
        'owing'                => 'ri-time-line',
        'pending_verification' => 'ri-loader-4-line',
        'paid'                 => 'ri-checkbox-circle-line',
    ];
@endphp

<div class="mx-auto w-full max-w-[1400px] px-5 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">

        {{-- ─── Page Header ──────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">My Dues</h1>
                <p class="mt-0.5 text-sm text-slate-500">Track, manage, and pay your departmental dues.</p>
            </div>
            @if ($hasOutstanding)
                <span class="inline-flex items-center gap-1.5 self-start rounded-md border border-[#0b3019]/20 bg-[#0b3019]/5 px-3 py-1.5 text-xs font-semibold text-[#0b3019] sm:self-auto">
                    <i class="ri-error-warning-line text-sm"></i>
                    {{ $outstandingCount }} outstanding due{{ $outstandingCount > 1 ? 's' : '' }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 self-start rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 sm:self-auto">
                    <i class="ri-checkbox-circle-line text-sm"></i>
                    All dues settled
                </span>
            @endif
        </div>

        {{-- ─── Flash Alerts ──────────────────────────────────────────────── --}}
        @if (session('status'))
            @php
                $msg          = session('status');
                $isDuesNotice = str_contains(strtolower($msg), 'dues') || str_contains(strtolower($msg), 'outstanding');
            @endphp
            @if ($isDuesNotice)
                <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <i class="ri-alarm-warning-line mt-0.5 shrink-0 text-base text-amber-500"></i>
                    <p>{{ $msg }}</p>
                </div>
            @else
                <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <i class="ri-check-line mt-0.5 shrink-0 text-base text-emerald-500"></i>
                    <p>{{ $msg }}</p>
                </div>
            @endif
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-xl border border-[#0b3019]/20 bg-[#0b3019]/5 px-4 py-3 text-sm text-[#0b3019]">
                <i class="ri-error-warning-line mt-0.5 shrink-0 text-base text-[#0b3019]/70"></i>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- ─── KPI Summary Cards ─────────────────────────────────────────── --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Outstanding Balance --}}
            <div class="rounded-xl border p-5 {{ $hasOutstanding ? 'border-[#0b3019] bg-[#0b3019]' : 'border-slate-200 bg-white' }}">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $hasOutstanding ? 'text-emerald-300' : 'text-slate-400' }}">Outstanding</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $hasOutstanding ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-400' }}">
                        <i class="ri-bill-line text-sm"></i>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold tracking-tight {{ $hasOutstanding ? 'text-white' : 'text-slate-900' }}" style="font-variant-numeric: tabular-nums">
                    GHS {{ number_format((float)($summary['outstanding_amount'] ?? 0), 2) }}
                </p>
                <p class="mt-1 text-xs {{ $hasOutstanding ? 'text-emerald-300/80' : 'text-slate-400' }}">
                    {{ $outstandingCount }} unpaid item{{ $outstandingCount !== 1 ? 's' : '' }}
                </p>
            </div>

            {{-- Total Paid --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Paid</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="ri-checkbox-circle-line text-sm"></i>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold tracking-tight text-slate-900" style="font-variant-numeric: tabular-nums">
                    GHS {{ number_format((float)($summary['paid_amount'] ?? 0), 2) }}
                </p>
                <p class="mt-1 text-xs text-slate-400">{{ $paidCount }} payment{{ $paidCount !== 1 ? 's' : '' }} recorded</p>
            </div>

            {{-- Next Due --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Next Due</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="ri-calendar-event-line text-sm"></i>
                    </span>
                </div>
                @if ($nextDue)
                    <p class="mt-3 text-sm font-semibold text-slate-900 truncate">{{ $nextDue['description'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ $nextDue['due_date'] ?? 'No due date' }} &middot; GHS {{ number_format($nextDue['amount'], 2) }}
                    </p>
                @else
                    <p class="mt-3 text-sm font-medium text-slate-400">No upcoming dues</p>
                    <p class="mt-1 text-xs text-slate-300">You're all caught up</p>
                @endif
            </div>

            {{-- Latest Payment --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Last Payment</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="ri-time-line text-sm"></i>
                    </span>
                </div>
                @if ($latestPayment)
                    <p class="mt-3 text-sm font-semibold text-slate-900 truncate">{{ $latestPayment['description'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $latestPayment['payment_date'] ?? '—' }}</p>
                @else
                    <p class="mt-3 text-sm font-medium text-slate-400">No payments yet</p>
                    <p class="mt-1 text-xs text-slate-300">&nbsp;</p>
                @endif
            </div>

        </div>

        {{-- ─── Dues Table Section ────────────────────────────────────────── --}}
        <div class="rounded-xl border border-slate-200 bg-white">

            {{-- Table Header: title + filter controls --}}
            <div class="border-b border-slate-100 px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Dues History</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Filter and search your complete dues record.</p>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form method="GET" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    {{-- Search --}}
                    <div class="flex-1">
                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Search</label>
                        <div class="relative">
                            <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="search"
                                name="search"
                                value="{{ e($searchTerm) }}"
                                placeholder="Description or reference…"
                                class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-8 pr-3 text-sm text-slate-700 placeholder-slate-400 transition focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0b3019]/15"
                            >
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="sm:w-44">
                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status</label>
                        <div class="relative">
                            <select name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-slate-50 pl-3 pr-8 text-sm text-slate-700 transition focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0b3019]/15">
                                <option value="">All statuses</option>
                                @foreach ($filterOptions['statuses'] ?? [] as $value => $label)
                                    <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    {{-- Academic Year --}}
                    <div class="sm:w-36">
                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Year</label>
                        <div class="relative">
                            <select name="academic_year" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-slate-50 pl-3 pr-8 text-sm text-slate-700 transition focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0b3019]/15">
                                <option value="">All years</option>
                                @foreach ($filterOptions['academic_years'] ?? [] as $year)
                                    <option value="{{ $year }}" @selected($activeYear === $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 sm:self-end">
                        <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                            <i class="ri-filter-3-line text-sm"></i>
                            Filter
                        </button>
                        @if ($isFiltered)
                            <a href="{{ route('student.dues.index') }}" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-[0.98]">
                                <i class="ri-close-line text-sm"></i>
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Pagination meta + per-page --}}
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-2.5 text-xs text-slate-500">
                <p>
                    Showing
                    <span class="font-semibold text-slate-700">{{ number_format($dues->firstItem() ?? 0) }}–{{ number_format($dues->lastItem() ?? 0) }}</span>
                    of <span class="font-semibold text-slate-700">{{ number_format($dues->total()) }}</span> entries
                </p>
                <form method="GET" class="flex items-center gap-2">
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ e($value) }}">
                    @endforeach
                    <label for="per_page" class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rows</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="h-7 rounded-md border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-0">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- ─── Desktop Table ─────────────────────────────────────────── --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead>
                        <tr class="bg-slate-50/40 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 text-left">Description</th>
                            <th class="px-5 py-3 text-left">Year</th>
                            <th class="px-5 py-3 text-left">Amount</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Due Date</th>
                            <th class="px-5 py-3 text-left">Reference</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dues as $due)
                            @php $status = $due->payment_status; @endphp
                            <tr class="group transition-colors hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900">{{ $due->description }}</p>
                                    @if ($due->payment_notes)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $due->payment_notes }}</p>
                                    @endif
                                    @if ($due->rejection_reason && $status === 'owing')
                                        <p class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-red-600">
                                            <i class="ri-error-warning-line"></i>
                                            Rejected: {{ $due->rejection_reason }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500">{{ $due->academic_year }}</td>
                                <td class="px-5 py-3.5 font-semibold text-slate-900 tabular-nums">
                                    GHS {{ number_format((float)$due->amount, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-[11px] font-semibold {{ $statusColors[$status] ?? 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200' }}">
                                        <i class="{{ $statusIcons[$status] ?? 'ri-question-line' }} text-xs"></i>
                                        {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500">
                                    {{ optional($due->due_date)->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">
                                    {{ $due->payment_reference ?? $due->reference_number ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($status === 'owing')
                                        @if (($paymentSettings['mode'] ?? 'automated') === 'automated')
                                            <form method="POST" action="{{ route('student.payments.rushpay.initialize', $due) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                                                    <i class="ri-secure-payment-line text-sm"></i>
                                                    Pay Now
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                @click="$dispatch('open-modal', 'manual-pay-{{ $due->due_id }}')"
                                                class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                                                <i class="ri-bank-card-line text-sm"></i>
                                                Pay
                                            </button>
                                            @include('dashboards.student.dues._manual-pay-modal', ['due' => $due])
                                        @endif
                                    @elseif ($status === 'pending_verification')
                                        <div class="text-right">
                                            <span class="text-xs font-semibold text-amber-600">Awaiting review</span>
                                            @if ($due->payment_method === 'manual')
                                                <p class="text-[10px] text-slate-400">Proof submitted</p>
                                            @endif
                                        </div>
                                    @elseif ($status === 'paid')
                                        <a href="{{ route('student.payments.paystack.receipt', $due) }}"
                                           class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98]">
                                            <i class="ri-file-download-line text-sm"></i>
                                            Receipt
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-300">
                                            <i class="ri-archive-drawer-line text-xl"></i>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-600">No dues found</p>
                                            <p class="mt-0.5 text-xs text-slate-400">
                                                @if ($isFiltered)
                                                    Try adjusting your filters. <a href="{{ route('student.dues.index') }}" class="font-semibold text-[#0b3019] underline">Clear filters</a>
                                                @else
                                                    No dues have been assigned to your account yet.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ─── Mobile Cards ───────────────────────────────────────────── --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($dues as $due)
                    @php $status = $due->payment_status; @endphp
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $due->description }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $due->academic_year }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-md px-2 py-1 text-[11px] font-semibold {{ $statusColors[$status] ?? 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200' }}">
                                <i class="{{ $statusIcons[$status] ?? 'ri-question-line' }} text-xs"></i>
                                {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-slate-400">Amount</p>
                                <p class="font-semibold text-slate-900 tabular-nums">GHS {{ number_format((float)$due->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Due Date</p>
                                <p class="font-medium text-slate-700">{{ optional($due->due_date)->format('M j, Y') ?? '—' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-slate-400">Reference</p>
                                <p class="font-mono font-medium text-slate-600">{{ $due->payment_reference ?? $due->reference_number ?? '—' }}</p>
                            </div>
                        </div>

                        @if ($due->payment_notes)
                            <p class="mt-2 text-xs text-slate-400">{{ $due->payment_notes }}</p>
                        @endif

                        @if ($due->rejection_reason && $status === 'owing')
                            <div class="mt-2 flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 ring-1 ring-inset ring-red-200">
                                <i class="ri-error-warning-line"></i>
                                Rejected: {{ $due->rejection_reason }}
                            </div>
                        @endif

                        <div class="mt-3">
                            @if ($status === 'owing')
                                @if (($paymentSettings['mode'] ?? 'automated') === 'automated')
                                    <form method="POST" action="{{ route('student.payments.rushpay.initialize', $due) }}">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg bg-[#0b3019] text-xs font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                                            <i class="ri-secure-payment-line text-sm"></i>
                                            Pay Now
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'manual-pay-{{ $due->due_id }}')"
                                        class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg bg-[#0b3019] text-xs font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                                        <i class="ri-bank-card-line text-sm"></i>
                                        Pay Manually
                                    </button>
                                    @include('dashboards.student.dues._manual-pay-modal', ['due' => $due])
                                @endif
                            @elseif ($status === 'pending_verification')
                                <p class="text-center text-xs font-semibold text-amber-600">
                                    <i class="ri-loader-4-line"></i> Awaiting verification
                                    @if ($due->payment_method === 'manual')
                                        &mdash; Proof submitted
                                    @endif
                                </p>
                            @elseif ($status === 'paid')
                                <a href="{{ route('student.payments.paystack.receipt', $due) }}"
                                   class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-[0.98]">
                                    <i class="ri-file-download-line text-sm"></i>
                                    Download Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-14 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-300">
                            <i class="ri-archive-drawer-line text-xl"></i>
                        </span>
                        <p class="mt-3 text-sm font-semibold text-slate-600">No dues found</p>
                        <p class="mt-1 text-xs text-slate-400">
                            @if ($isFiltered)
                                <a href="{{ route('student.dues.index') }}" class="font-semibold text-[#0b3019] underline">Clear filters</a> to see all dues.
                            @else
                                No dues have been assigned to your account yet.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- ─── Pagination ─────────────────────────────────────────────── --}}
            @if ($dues->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-400">
                        Page {{ number_format($dues->currentPage()) }} of {{ number_format($dues->lastPage()) }}
                    </p>
                    <div>
                        {{ $dues->onEachSide(1)->links('vendor.pagination.data-limit') }}
                    </div>
                </div>
            @endif

        </div>{{-- end table section card --}}

    </div>{{-- end space-y-6 --}}
</div>

</x-layouts.dashboard>
