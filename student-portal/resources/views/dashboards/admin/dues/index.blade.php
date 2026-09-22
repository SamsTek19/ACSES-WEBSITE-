@php($title = 'Student dues')
@php($statusLabels = [
    'owing' => 'Owing',
    'pending_verification' => 'Pending verification',
    'paid' => 'Paid',
])

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-[1400px] space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <i class="ri-money-dollar-circle-line text-sm" aria-hidden="true"></i>
                    <span>Student dues</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Academic dues</h1>
                <p class="text-sm text-slate-500">Review issued dues, monitor collections, and configure payment gateways.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('admin.dues.create') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                    <i class="ri-add-line text-sm" aria-hidden="true"></i>
                    <span>Create due</span>
                </a>
                <a href="{{ route('admin.dues.export', request()->query()) }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <i class="ri-download-2-line text-sm" aria-hidden="true"></i>
                    Export
                </a>
                <a href="{{ route('admin.dues.statistics', request()->query()) }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <i class="ri-pie-chart-2-line text-sm" aria-hidden="true"></i>
                    Analytics
                </a>
                <button type="button" @click="$dispatch('open-modal', 'global-payment-settings')" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-[#0b3019]/20 bg-white px-3 text-xs font-semibold text-[#0b3019] shadow-sm transition hover:bg-[#0b3019]/5 active:scale-95">
                    <i class="ri-settings-4-line text-sm"></i>
                    Settings
                </button>
            </div>
        </header>

        <!-- Payment Settings Modal -->
        <x-modal name="global-payment-settings" focusable>
            <form x-data="{ paymentMode: '{{ $paymentSettings['payment_mode'] ?? 'automated' }}' }" action="{{ route('admin.dues.settings.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">Global Payment Configuration</h2>
                    <p class="mt-1 text-xs text-slate-500">Configure portal-wide payment routes for academic dues.</p>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Payment Gateway Mode</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" x-model="paymentMode" name="payment_mode" value="automated" class="text-[#0b3019] focus:ring-[#0b3019]">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition">Automated (RushPay)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" x-model="paymentMode" name="payment_mode" value="manual" class="text-[#0b3019] focus:ring-[#0b3019]">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition">Manual Verification</span>
                        </label>
                    </div>
                </div>

                <div x-show="paymentMode === 'manual'" x-collapse class="grid gap-4 border-t border-slate-100 pt-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-[#0b3019]">Manual Bank Details</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bank Name</label>
                                <input type="text" name="manual_bank_name" value="{{ $paymentSettings['manual_bank_name'] ?? '' }}" placeholder="GCB Bank" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Account Name</label>
                                <input type="text" name="manual_account_name" value="{{ $paymentSettings['manual_account_name'] ?? '' }}" placeholder="ACSES UMaT" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Account Number</label>
                                <input type="text" name="manual_account_number" value="{{ $paymentSettings['manual_account_number'] ?? '' }}" placeholder="1234567890" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-[#0b3019]">Mobile Money / Instructions</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Momo Number</label>
                                <input type="text" name="manual_momo_number" value="{{ $paymentSettings['manual_momo_number'] ?? '' }}" placeholder="0541234567" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Momo Merchant Name</label>
                                <input type="text" name="manual_momo_name" value="{{ $paymentSettings['manual_momo_name'] ?? '' }}" placeholder="ACSES President" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Instructions</label>
                                <textarea name="manual_instructions" rows="2" placeholder="State instructions..." class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]/30">{{ $paymentSettings['manual_instructions'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close')" class="h-9 px-4 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition active:scale-95">Cancel</button>
                    <button type="submit" class="h-9 px-4 rounded-lg text-xs font-semibold text-white bg-[#0b3019] hover:bg-[#072412] transition shadow-sm active:scale-95">Save settings</button>
                </div>
            </form>
        </x-modal>

        <!-- Bento Financial Summary Grid -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-[#0b3019]/30 bg-gradient-to-br from-[#0b3019] to-[#06180d] px-6 py-5 text-white shadow-md">
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-400">Total Invoices</p>
                <p class="mt-3 text-3xl font-extrabold">{{ number_format($totals['count'] ?? 0) }}</p>
                <p class="mt-2 text-[9px] uppercase tracking-wider text-emerald-300/70">Across active filters</p>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white px-6 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Outstanding Balance</p>
                <p class="mt-3 text-2xl font-extrabold text-[#0b3019]">GHS {{ number_format((float) ($totals['outstanding'] ?? 0), 2) }}</p>
                <p class="mt-2 text-[9px] text-slate-500 leading-normal">Sum of owing & pending verification.</p>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white px-6 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Total Billed</p>
                <p class="mt-3 text-2xl font-extrabold text-slate-800">GHS {{ number_format((float) ($totals['total'] ?? 0), 2) }}</p>
                <p class="mt-2 text-[9px] text-slate-500 leading-normal">Includes paid, pending, and owing.</p>
            </article>

            <article class="rounded-2xl border border-emerald-100 bg-emerald-50/50 px-6 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-700">Total Collected</p>
                <p class="mt-3 text-2xl font-extrabold text-emerald-800">GHS {{ number_format((float) ($totals['paid'] ?? 0), 2) }}</p>
                <p class="mt-2 text-[9px] text-emerald-600/90 leading-normal">Confirmed successfully processed.</p>
            </article>
        </section>

        <!-- Filters -->
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">
        <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4 space-y-0">
            <form method="GET" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
                @foreach (request()->except(['search', 'academic_year', 'status', 'class', 'year', 'per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <div class="sm:col-span-2 flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search student</label>
                    <div class="relative">
                        <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, ref ID, username..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 pl-9 pr-4 text-xs text-slate-800 focus:bg-white focus:border-[#0b3019]/60 focus:outline-none transition-all">
                    </div>
                </div>

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

                <div class="lg:col-span-6 flex items-center justify-end gap-2 pt-2">
                    <a href="{{ route('admin.dues.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Reset</a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-filter-3-line text-sm"></i>
                        Apply filters
                    </button>
                </div>
            </form>
        </div>

            <!-- Table Rows Meta -->
            <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs font-semibold text-slate-600">Showing {{ $dues->firstItem() ?? 0 }}–{{ $dues->lastItem() ?? 0 }} of {{ $dues->total() }} issued dues</p>
                <form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                    <select id="per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" x-on:change="$el.form.submit()">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Transactions Registry List -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3">Student Profile</th>
                            <th class="px-5 py-3">Reference Code</th>
                            <th class="px-5 py-3">Academic Term</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Due Date</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($dues as $due)
                            <tr class="transition hover:bg-slate-50/50 cursor-pointer" onclick="if(!event.target.closest('a') && !event.target.closest('button')) window.location='{{ route('admin.students.show', $due->student_id) }}'">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.students.show', $due->student_id) }}" class="flex items-center gap-3 group/std-link hover:opacity-90">
                                        <!-- Avatar Profile Wrapper -->
                                        <?php
                                            $stName = trim($due->student?->fullname ?? $due->student?->username ?? 'Student');
                                            $stInitials = collect(preg_split('/\s+/', $stName))->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
                                            $stImage = $due->student?->profile_picture ? asset('storage/' . $due->student->profile_picture) : null;
                                        ?>
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full border border-[#0b3019]/10 bg-slate-100 group-hover/std-link:border-[#0b3019]/35 group-hover/std-link:scale-105 transition-all duration-200">
                                            @if ($stImage)
                                                <img src="{{ $stImage }}" alt="Profile photo" class="h-full w-full object-cover" />
                                            @else
                                                <span class="text-xs font-bold text-[#0b3019]">{{ $stInitials ?: 'ST' }}</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-bold text-slate-900 text-xs group-hover/std-link:text-[#0b3019] transition-colors duration-200">{{ $stName }}</span>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ $due->student?->email ?? 'No email' }}</span>
                                            <span class="text-[9px] text-[#0b3019] font-bold">{{ $due->student?->class ?? '—' }} · Year {{ $due->student?->year ?? '—' }}</span>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-600">
                                    #{{ $due->payment_reference ?? $due->reference_number ?? $due->due_id }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-500">
                                    {{ $due->academic_year }}
                                </td>
                                <td class="px-5 py-3.5 font-extrabold text-slate-900">
                                    GHS {{ number_format((float) $due->amount, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 font-medium">
                                    {{ optional($due->due_date)->format('M j, Y') ?? $due->due_date }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php($status = $due->payment_status)
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-md px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider',
                                        'bg-rose-50 text-rose-700 border border-rose-200/50' => $status === 'owing',
                                        'bg-amber-50 text-amber-700 border border-amber-200/50' => $status === 'pending_verification',
                                        'bg-emerald-50 text-emerald-800 border border-emerald-200/50' => $status === 'paid',
                                        'bg-slate-100 text-slate-600 border border-slate-200/50' => ! in_array($status, array_keys($statusLabels), true),
                                    ])>
                                        <span @class([
                                            'h-1 w-1 rounded-full',
                                            'bg-rose-500' => $status === 'owing',
                                            'bg-amber-500 animate-pulse' => $status === 'pending_verification',
                                            'bg-emerald-600' => $status === 'paid',
                                            'bg-slate-500' => ! in_array($status, array_keys($statusLabels), true),
                                        ])></span>
                                        {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="ri-inbox-line text-3xl text-slate-300"></i>
                                        <p class="font-bold text-slate-600">No records found</p>
                                        <p class="text-xs text-slate-400">Try adjusting your active directory filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs text-slate-400">Page {{ $dues->currentPage() }} of {{ $dues->lastPage() }}</p>
                <div class="flex justify-center sm:ml-auto sm:justify-end">
                    {{ $dues->onEachSide(1)->links('vendor.pagination.data-limit') }}
                </div>
            </div>

        </section>
    </div>
</x-layouts.admin>
