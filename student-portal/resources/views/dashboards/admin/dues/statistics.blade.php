<?php
    $title = 'Dues performance analytics';
    $stats = $stats ?? [];
?>

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-[1400px] space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dues.index', request()->query()) }}" class="hover:text-[#0b3019] transition-colors">Dues</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Analytics</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Revenue &amp; collection insights</h1>
                <p class="text-sm text-slate-500">Track academic inflows, collection rates, and cohort performances.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.dues.index', request()->query()) }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <i class="ri-arrow-left-line text-sm"></i>
                    Back to dues
                </a>
            </div>
        </header>

        <!-- Filters -->
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">
        <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4 space-y-0">
            <form method="GET" class="grid gap-4 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5">
                @foreach (request()->except(['search', 'academic_year', 'status', 'class', 'year']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic Year</label>
                    <div class="relative">
                        <select name="academic_year" class="h-9 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3 pr-8 text-xs text-slate-800 focus:bg-white focus:border-[#0b3019]/60 focus:outline-none transition-all">
                            <option value="">All years</option>
                            @foreach ($filtersMeta['academic_years'] as $yearOption)
                                <option value="{{ $yearOption }}" @selected(($filters['academic_year'] ?? '') === $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                    <div class="relative">
                        <select name="status" class="h-9 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3 pr-8 text-xs text-slate-800 focus:bg-white focus:border-[#0b3019]/60 focus:outline-none transition-all">
                            <option value="">All statuses</option>
                            @foreach ($filtersMeta['statuses'] as $statusValue => $statusLabel)
                                <option value="{{ $statusValue }}" @selected(($filters['status'] ?? '') === $statusValue)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Class</label>
                    <div class="relative">
                        <select name="class" class="h-9 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3 pr-8 text-xs text-slate-800 focus:bg-white focus:border-[#0b3019]/60 focus:outline-none transition-all">
                            <option value="">All classes</option>
                            @foreach ($filtersMeta['classes'] as $classOption)
                                <option value="{{ $classOption }}" @selected(($filters['class'] ?? '') === $classOption)>{{ $classOption }}</option>
                            @endforeach
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Year Level</label>
                    <div class="relative">
                        <select name="year" class="h-9 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 pl-3 pr-8 text-xs text-slate-800 focus:bg-white focus:border-[#0b3019]/60 focus:outline-none transition-all">
                            <option value="">All levels</option>
                            @foreach ($filtersMeta['years'] as $yearOption)
                                <option value="{{ $yearOption }}" @selected(($filters['year'] ?? '') == $yearOption)>Year {{ $yearOption }}</option>
                            @endforeach
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex items-end justify-end gap-2 pt-2 sm:col-span-2 md:col-span-1">
                    <a href="{{ route('admin.dues.statistics') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Reset</a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-filter-3-line text-sm"></i>
                        Apply filters
                    </button>
                </div>
            </form>
        </div>
        </section>

        <!-- Bento Overview Metrics -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-2xl border border-[#0b3019]/30 bg-gradient-to-br from-[#0b3019] via-[#0d3f21] to-[#051e0f] p-6 text-white shadow-md flex flex-col justify-between">
                <header class="flex items-start justify-between">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-400">Total Collected</p>
                        <p class="text-3xl font-extrabold">GHS {{ number_format((float) data_get($stats, 'totals.paid_amount', 0), 2) }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/10 backdrop-blur-md text-emerald-300">
                        <i class="ri-coins-line text-xl"></i>
                    </span>
                </header>
                <div class="mt-6 flex items-center justify-between text-[10px] text-emerald-200/80 border-t border-white/5 pt-4">
                    <span class="font-bold uppercase tracking-wider">Invoices paid</span>
                    <span class="font-extrabold text-white">{{ number_format((int) data_get($stats, 'totals.paid_count', 0)) }}</span>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm flex flex-col justify-between">
                <header class="flex items-start justify-between">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Outstanding Balance</p>
                        <p class="text-3xl font-extrabold text-slate-800">GHS {{ number_format((float) data_get($stats, 'totals.outstanding_amount', 0), 2) }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="ri-alert-line text-xl"></i>
                    </span>
                </header>
                <div class="mt-6 flex items-center justify-between text-[10px] text-slate-500 border-t border-slate-100 pt-4">
                    <span class="font-bold uppercase tracking-wider">Pending / Owing accounts</span>
                    <span class="font-extrabold text-[#0b3019]">{{ number_format((int) data_get($stats, 'totals.invoice_count', 0) - (int) data_get($stats, 'totals.paid_count', 0)) }}</span>
                </div>
            </article>

            <?php
                $collectionRate = (float) data_get($stats, 'totals.collection_rate', 0);
            ?>
            <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm flex flex-col justify-between">
                <header class="flex items-start justify-between">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Collection Rate</p>
                        <p class="text-3xl font-extrabold text-slate-800">{{ number_format($collectionRate, 2) }}%</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <i class="ri-percent-line text-xl"></i>
                    </span>
                </header>
                <div class="mt-6 space-y-2 border-t border-slate-100 pt-4">
                    <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-[#0b3019]" style="width: {{ $collectionRate }}%"></div>
                    </div>
                </div>
            </article>
        </section>

        <!-- Collections Split Linear Progression -->
        <?php
            $paidAmount = (float) data_get($stats, 'totals.paid_amount', 0);
            $outstandingAmount = (float) data_get($stats, 'totals.outstanding_amount', 0);
            $totalAmount = $paidAmount + $outstandingAmount;
            $paidShare = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 1) : 0;
            $outstandingShare = $totalAmount > 0 ? round(($outstandingAmount / $totalAmount) * 100, 1) : 0;
        ?>
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 space-y-2.5">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <span>Collections split by amount</span>
                <span class="flex items-center gap-3 font-medium normal-case tracking-normal text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                        Paid {{ $paidShare }}%
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                        Outstanding {{ $outstandingShare }}%
                    </span>
                </span>
            </div>
            <div class="flex h-2 w-full overflow-hidden rounded-full bg-white border border-slate-200/30">
                <div class="h-full bg-emerald-500 rounded-l-full" style="width: {{ $paidShare }}%"></div>
                <div class="h-full bg-amber-400 rounded-r-full" style="width: {{ $outstandingShare }}%"></div>
            </div>
        </div>

        <!-- Leadership Bento Grid -->
        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-amber-600">
                    <i class="ri-vip-crown-2-line text-lg"></i>
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Best Class Cohort</h3>
                </div>
                @if (data_get($stats, 'leaders.best_class'))
                    <p class="text-base font-extrabold text-[#0b3019] leading-snug truncate">{{ data_get($stats, 'leaders.best_class.label') }}</p>
                    <p class="text-xs text-slate-500">Collected GHS <strong class="text-slate-800">{{ number_format((float) data_get($stats, 'leaders.best_class.amount'), 2) }}</strong></p>
                @else
                    <p class="text-xs text-slate-400 font-medium">No collection metrics available</p>
                @endif
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-indigo-600">
                    <i class="ri-medal-line text-lg"></i>
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Best Year Cohort</h3>
                </div>
                @if (data_get($stats, 'leaders.best_year'))
                    <p class="text-base font-extrabold text-[#0b3019] leading-snug truncate">{{ data_get($stats, 'leaders.best_year.label') }}</p>
                    <p class="text-xs text-slate-500">Collected GHS <strong class="text-slate-800">{{ number_format((float) data_get($stats, 'leaders.best_year.amount'), 2) }}</strong></p>
                @else
                    <p class="text-xs text-slate-400 font-medium">No collection metrics available</p>
                @endif
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-emerald-600">
                    <i class="ri-award-line text-lg"></i>
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Combined Leader</h3>
                </div>
                @if (data_get($stats, 'leaders.best_class_year'))
                    <p class="text-base font-extrabold text-[#0b3019] leading-snug truncate">{{ data_get($stats, 'leaders.best_class_year.label') }}</p>
                    <p class="text-xs text-slate-500">Collected GHS <strong class="text-slate-800">{{ number_format((float) data_get($stats, 'leaders.best_class_year.amount'), 2) }}</strong></p>
                @else
                    <p class="text-xs text-slate-400 font-medium">No collection metrics available</p>
                @endif
            </article>
        </section>

        <!-- Charts Grid Section -->
        <section class="grid gap-6 md:grid-cols-2">
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Collections Mix</h3>
                    <p class="text-[10px] text-slate-500">Breakdown share of paid vs owing dues by value.</p>
                </div>
                <div class="flex items-center justify-center py-4 h-56">
                    <canvas id="dues-status-pie" class="max-h-full max-w-full"></canvas>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Contributing Classes</h3>
                    <p class="text-[10px] text-slate-500">Contributing classes ordered by collected paid amounts.</p>
                </div>
                <div class="flex items-center justify-center py-4 h-56">
                    <canvas id="dues-class-bar" class="max-h-full max-w-full"></canvas>
                </div>
            </article>
        </section>

        <!-- Lists of Cohorts Performances -->
        <section class="grid gap-6 md:grid-cols-2">
            
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Class Inflow Share</h3>
                    <p class="text-[10px] text-slate-500">Dues collections share and paid totals per programme class.</p>
                </div>
                <ul class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                    @forelse (data_get($stats, 'breakdowns.classes', []) as $class)
                        @php($classShare = (float) ($class['share'] ?? 0))
                        <li class="relative overflow-hidden rounded-xl border border-slate-100 bg-slate-50/50 px-3.5 py-2.5 text-xs">
                            <!-- Inflow progress overlay -->
                            <div class="pointer-events-none absolute inset-0 rounded-l-md bg-emerald-500/[0.04]" style="width: {{ $classShare }}%"></div>
                            
                            <div class="relative flex items-center justify-between gap-4">
                                <span class="font-bold text-slate-700 truncate">{{ $class['label'] }}</span>
                                <span class="font-semibold text-slate-900 shrink-0">GHS {{ number_format((float) $class['amount'], 2) }} <span class="text-slate-400 font-medium">({{ $class['share'] }}%)</span></span>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-8 text-center text-xs text-slate-400">
                            No collection logs loaded.
                        </li>
                    @endforelse
                </ul>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Year Level Inflow Share</h3>
                    <p class="text-[10px] text-slate-500">Dues collections share and paid totals per student year levels.</p>
                </div>
                <ul class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                    @forelse (data_get($stats, 'breakdowns.years', []) as $year)
                        @php($yearShare = (float) ($year['share'] ?? 0))
                        <li class="relative overflow-hidden rounded-xl border border-slate-100 bg-slate-50/50 px-3.5 py-2.5 text-xs">
                            <!-- Inflow progress overlay -->
                            <div class="pointer-events-none absolute inset-0 rounded-l-md bg-emerald-500/[0.04]" style="width: {{ $yearShare }}%"></div>
                            
                            <div class="relative flex items-center justify-between gap-4">
                                <span class="font-bold text-slate-700 truncate">{{ $year['label'] }}</span>
                                <span class="font-semibold text-slate-900 shrink-0">GHS {{ number_format((float) $year['amount'], 2) }} <span class="text-slate-400 font-medium">({{ $year['share'] }}%)</span></span>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-8 text-center text-xs text-slate-400">
                            No collection logs loaded.
                        </li>
                    @endforelse
                </ul>
            </article>

        </section>


    </div>

    <?php
        $chartStatusLabels = ['Paid', 'Outstanding'];
        $chartStatusData = [
            (float) data_get($stats, 'totals.paid_amount', 0),
            (float) data_get($stats, 'totals.outstanding_amount', 0),
        ];

        $classBreakdownForChart = collect(data_get($stats, 'breakdowns.classes', []))->take(8);
        $chartClassLabels = $classBreakdownForChart->pluck('label')->values();
        $chartClassData = $classBreakdownForChart->pluck('amount')->map(fn ($v) => (float) $v)->values();
    ?>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pieEl = document.getElementById('dues-status-pie');
                const barEl = document.getElementById('dues-class-bar');

                const statusLabels = @json($chartStatusLabels);
                const statusData = @json($chartStatusData);
                const classLabels = @json($chartClassLabels);
                const classData = @json($chartClassData);

                if (pieEl && window.Chart && statusData.some(function (v) { return v > 0; })) {
                    new Chart(pieEl.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: ['#0b3019', '#f59e0b'],
                                borderColor: ['#ffffff', '#ffffff'],
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 10,
                                        font: { size: 10, weight: 'bold' },
                                    },
                                },
                            },
                            cutout: '70%',
                        },
                    });
                }

                if (barEl && window.Chart && classLabels.length > 0) {
                    new Chart(barEl.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: classLabels,
                            datasets: [{
                                data: classData,
                                backgroundColor: '#0b3019',
                                borderRadius: 8,
                                borderSkipped: false,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 9, weight: 'bold' },
                                        color: '#64748b'
                                    },
                                },
                                y: {
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        font: { size: 9 },
                                        color: '#64748b'
                                    },
                                    beginAtZero: true,
                                },
                            },
                        },
                    });
                }
            });
        </script>
    @endpush
</x-layouts.admin>
