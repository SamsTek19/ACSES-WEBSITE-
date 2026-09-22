@php($title = $title ?? 'Pending Registrations')

<x-layouts.admin :title="$title">
    <div x-data="{ rejectModalOpen: false, bulkRejectModalOpen: false, selectedId: null, selectedIds: [], selectAll: false }" 
         class="relative">
        
        {{-- Main Content --}}
        <div class="mx-auto w-full max-w-6xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

            {{-- Header --}}
            <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <i class="ri-user-add-line text-sm" aria-hidden="true"></i>
                        <span>Registration verification</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pending registrations</h1>
                    <p class="text-sm text-slate-500">Review and verify student registration requests. Approve or reject applications.</p>
                </div>
            </header>

            {{-- Statistics Cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 animate-fade-slide animate-fade-slide-delay-200">
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <i class="ri-time-line text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ $statistics['pending'] }}</p>
                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <i class="ri-checkbox-circle-line text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ $statistics['approved'] }}</p>
                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Approved</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                            <i class="ri-close-circle-line text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ $statistics['rejected'] }}</p>
                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Rejected</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                            <i class="ri-file-list-3-line text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold leading-none tabular-nums text-slate-900">{{ $statistics['total'] }}</p>
                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</p>
                        </div>
                    </div>
                </article>
            </div>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
                    <div class="flex items-center gap-2">
                        <i class="ri-check-double-line text-base text-emerald-600" aria-hidden="true"></i>
                        <p>{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <div class="flex items-center gap-2">
                        <i class="ri-error-warning-line text-base" aria-hidden="true"></i>
                        <p>{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            {{-- Filters --}}
            <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">
                <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                    <form method="GET" class="flex flex-wrap items-end gap-3">
                        <input type="hidden" name="per_page" value="{{ request('per_page', $currentPerPage) }}">

                        <div class="flex w-full flex-col gap-1.5 md:w-56">
                            <label for="filter_search" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                            <div class="relative">
                                <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="filter_search" type="search" name="search" value="{{ $search }}" placeholder="Name, email, index…" class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20" />
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-1.5 md:w-36">
                            <label for="filter_status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                            <div class="relative">
                                <select id="filter_status" name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="" @selected($activeStatus === null || $activeStatus === '')>All statuses</option>
                                    @foreach ($statuses as $statusOption)
                                        <option value="{{ $statusOption }}" @selected($activeStatus === $statusOption)>{{ Str::title($statusOption) }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-1.5 md:w-44">
                            <label for="filter_class" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program</label>
                            <div class="relative">
                                <select id="filter_class" name="class" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="" @selected($activeClass === null || $activeClass === '')>All programs</option>
                                    @foreach ($classOptions as $option)
                                        <option value="{{ $option }}" @selected($activeClass === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-1.5 md:w-28">
                            <label for="filter_year" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Year</label>
                            <div class="relative">
                                <select id="filter_year" name="year" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="" @selected($activeYear === null || $activeYear === '')>All years</option>
                                    @foreach ($yearOptions as $option)
                                        <option value="{{ $option }}" @selected($activeYear === $option)>Year {{ $option }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="flex items-end gap-2 md:ml-auto">
                            <a href="{{ route('admin.pending-registrations.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                                Reset
                            </a>
                            <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                                <i class="ri-equalizer-line text-sm"></i>
                                Apply filters
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- Bulk Actions Bar --}}
            <div x-show="selectedIds.length > 0" x-cloak x-transition class="rounded-xl border border-[#0b3019]/15 bg-[#0b3019]/5 px-4 py-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-[#0b3019]">
                        <span x-text="selectedIds.length"></span> selected
                    </p>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.pending-registrations.bulk') }}" class="inline">
                            @csrf
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                                <i class="ri-checkbox-circle-line text-sm"></i>
                                Bulk approve
                            </button>
                        </form>
                        <button type="button" @click="bulkRejectModalOpen = true" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700 active:scale-95">
                            <i class="ri-close-circle-line text-sm"></i>
                            Bulk reject
                        </button>
                    </div>
                </div>
            </div>

            {{-- Registrations Table --}}
            <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-400">
                {{-- Header bar: select-all + count + rows --}}
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/40 px-4 py-2.5">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? @js($registrations->pluck('id')) : []" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]">
                        <span class="text-xs font-semibold text-slate-500">Select all</span>
                    </div>
                    <form method="GET" class="flex items-center gap-2">
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

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="w-12 px-4 py-3"></th>
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Program</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($registrations as $registration)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" 
                                               :value="{{ $registration->id }}" 
                                               x-model.number="selectedIds"
                                               class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]">
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-slate-900">{{ $registration->fullname }}</span>
                                            <span class="text-xs text-slate-500">{{ $registration->email }}</span>
                                            <span class="text-xs text-slate-400">Ref: {{ $registration->index_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700">{{ $registration->class }}</span>
                                            <span class="text-xs text-slate-400">Year {{ $registration->year }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ match($registration->status) {
                                            'approved' => 'bg-emerald-50 text-emerald-700',
                                            'rejected' => 'bg-rose-50 text-rose-600',
                                            default => 'bg-amber-50 text-amber-700',
                                        } }}">
                                            <i class="{{ match($registration->status) {
                                                'approved' => 'ri-checkbox-circle-line',
                                                'rejected' => 'ri-close-circle-line',
                                                default => 'ri-time-line',
                                            } }} text-sm"></i>
                                            {{ Str::title($registration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-500">
                                        {{ $registration->created_at->format('M j, Y') }}
                                        <span class="text-slate-400">{{ $registration->created_at->format('g:i A') }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($registration->isPending() || $registration->isRejected())
                                                <form method="POST" action="{{ route('admin.pending-registrations.approve', $registration) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-200" title="Approve">
                                                        <i class="ri-checkbox-circle-line"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($registration->isPending() || $registration->isApproved())
                                                <button type="button" @click="selectedId = {{ $registration->id }}; rejectModalOpen = true" class="inline-flex items-center gap-1 rounded-lg bg-rose-100 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-200" title="Reject">
                                                    <i class="ri-close-circle-line"></i>
                                                    Reject
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.pending-registrations.show', $registration) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/40 hover:text-[#0b3019]" title="View Details">
                                                <i class="ri-eye-line"></i>
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <i class="ri-user-search-line text-4xl text-slate-300"></i>
                                            <p class="font-semibold text-slate-600">No registrations found.</p>
                                            <p>Adjust filters or check back later.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse ($registrations as $registration)
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $registration->fullname }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $registration->email }}</p>
                                    <p class="text-xs text-slate-400">{{ $registration->class }} · Year {{ $registration->year }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ match($registration->status) {
                                    'approved' => 'bg-emerald-50 text-emerald-700',
                                    'rejected' => 'bg-rose-50 text-rose-600',
                                    default => 'bg-amber-50 text-amber-700',
                                } }}">
                                    {{ Str::title($registration->status) }}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs tabular-nums text-slate-400">{{ $registration->created_at->format('M j, Y') }}</span>
                                <div class="flex gap-2">
                                    @if ($registration->isPending() || $registration->isRejected())
                                        <form method="POST" action="{{ route('admin.pending-registrations.approve', $registration) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="h-7 inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                <i class="ri-checkbox-circle-line"></i>
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if ($registration->isPending() || $registration->isApproved())
                                        <button type="button" @click="selectedId = {{ $registration->id }}; rejectModalOpen = true" class="h-7 inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            <i class="ri-close-circle-line"></i>
                                            Reject
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.pending-registrations.show', $registration) }}" class="h-7 inline-flex items-center gap-1 rounded-md border border-slate-200 px-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                        <i class="ri-eye-line"></i>
                                        View
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center">
                            <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                                <i class="ri-user-search-line text-2xl text-slate-300"></i>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No registrations found</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs text-slate-400">Showing {{ $registrations->firstItem() ?? 0 }}–{{ $registrations->lastItem() ?? 0 }} of {{ $registrations->total() }} registrations</p>
                    <div class="flex justify-center sm:ml-auto sm:justify-end">
                        {{ $registrations->onEachSide(1)->links('vendor.pagination.data-limit') }}
                    </div>
                </div>
            </section>
        </div>

        {{-- Reject Modal (Individual) --}}
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-transition.opacity>
            <div @click.outside="rejectModalOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900">Reject Registration</h3>
                <p class="mt-1 text-sm text-slate-500">Please provide a reason for rejecting this registration. This will be sent to the student.</p>
                
                <form method="POST" :action="'/admin/pending-registrations/' + selectedId + '/reject'" class="mt-4">
                    @csrf
                    <div class="space-y-3">
                        <label for="rejection_reason" class="block text-sm font-medium text-slate-700">Rejection Reason</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30" placeholder="Explain why this registration is being rejected..."></textarea>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="rejectModalOpen = false" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                            Reject Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Reject Modal --}}
        <div x-show="bulkRejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-transition.opacity>
            <div @click.outside="bulkRejectModalOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900">Bulk Reject Registrations</h3>
                <p class="mt-1 text-sm text-slate-500">You are about to reject <span class="font-semibold text-rose-600" x-text="selectedIds.length"></span> registration(s). Please provide a reason.</p>
                
                <form method="POST" action="{{ route('admin.pending-registrations.bulk') }}" class="mt-4">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="space-y-3">
                        <label for="bulk_rejection_reason" class="block text-sm font-medium text-slate-700">Rejection Reason</label>
                        <textarea id="bulk_rejection_reason" name="rejection_reason" rows="4" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30" placeholder="Explain why these registrations are being rejected..."></textarea>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="bulkRejectModalOpen = false" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                            Reject Selected
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
