@php($title = ($student->fullname ?? $student->username) . ' · Student profile')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-5xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.students.index') }}" class="hover:text-[#0b3019] transition-colors">Students</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Profile</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $student->fullname ?? $student->username }}</h1>
                <div class="flex flex-wrap gap-1.5 mt-1">
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                        <i class="ri-at-line text-xs"></i>
                        {{ $student->username }}
                    </span>
                    @if ($student->index_number)
                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                            <i class="ri-hashtag text-xs"></i>
                            {{ $student->index_number }}
                        </span>
                    @endif
                    @if ($student->class)
                        <span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-xs font-semibold text-[#0b3019]">
                            <i class="ri-community-line text-xs"></i>
                            {{ $student->class }}
                        </span>
                    @endif
                    @if ($student->year)
                        <span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-xs font-semibold text-[#0b3019]">
                            <i class="ri-calendar-2-line text-xs"></i>
                            Year {{ $student->year }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('admin.students.edit', $student) }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <i class="ri-pencil-line text-sm"></i>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Delete this student account? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 text-xs font-semibold text-rose-600 shadow-sm transition hover:bg-rose-50 active:scale-95">
                        <i class="ri-delete-bin-line text-sm"></i>
                        Delete
                    </button>
                </form>
            </div>
        </header>

        <!-- Info cards -->
        <section class="grid gap-5 lg:grid-cols-2 animate-fade-slide animate-fade-slide-delay-200">
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <h2 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Contact &amp; identity</h2>
                <dl class="mt-4 divide-y divide-slate-50 text-sm text-slate-600">
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-user-line text-sm text-[#0b3019]/60"></i> Full name</dt>
                        <dd class="font-semibold text-slate-900 text-xs text-right">{{ $student->fullname ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-mail-line text-sm text-[#0b3019]/60"></i> Email</dt>
                        <dd class="font-semibold text-slate-900 text-xs text-right">{{ $student->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-phone-line text-sm text-[#0b3019]/60"></i> Phone</dt>
                        <dd class="font-semibold text-slate-900 text-xs text-right">{{ $student->phone_number ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-hashtag text-sm text-[#0b3019]/60"></i> Index number</dt>
                        <dd class="font-semibold text-slate-900 text-xs text-right">{{ $student->index_number ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-calendar-line text-sm text-[#0b3019]/60"></i> Joined</dt>
                        <dd class="font-semibold text-slate-900 text-xs text-right tabular-nums">{{ $student->created_at?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </article>

            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <h2 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic placement</h2>
                <dl class="mt-4 divide-y divide-slate-50 text-sm text-slate-600">
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-book-open-line text-sm text-[#0b3019]/60"></i> Programme</dt>
                        <dd class="font-semibold text-slate-900 text-xs">{{ $student->class ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 py-2.5">
                        <dt class="flex items-center gap-2 text-slate-400 text-xs"><i class="ri-medal-line text-sm text-[#0b3019]/60"></i> Year level</dt>
                        <dd class="font-semibold text-slate-900 text-xs">{{ $student->year ? 'Year ' . $student->year : '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-5 pt-4 border-t border-slate-100">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Quick actions</h3>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <a href="mailto:{{ $student->email }}" class="inline-flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                            <span>Email student</span>
                            <i class="ri-mail-line text-sm"></i>
                        </a>
                        @if ($student->phone_number)
                            <a href="tel:{{ $student->phone_number }}" class="inline-flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                <span>Call student</span>
                                <i class="ri-phone-line text-sm"></i>
                            </a>
                        @endif
                        <a href="{{ route('admin.dues.index', ['search' => $student->username]) }}" class="inline-flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                            <span>View all dues</span>
                            <i class="ri-money-dollar-circle-line text-sm"></i>
                        </a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                            <span>Edit credentials</span>
                            <i class="ri-pencil-line text-sm"></i>
                        </a>
                    </div>
                </div>
            </article>
        </section>

        <!-- Dues Ledger -->
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-400">
            <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                <div class="flex items-center gap-2">
                    <i class="ri-money-dollar-circle-line text-base text-[#0b3019]/60"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">Dues ledger &amp; status history</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3">Description</th>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Academic term</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Due date</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @php($statusLabels = ['owing' => 'Owing', 'pending_verification' => 'Pending', 'paid' => 'Paid'])
                        @forelse ($student->dues as $due)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $due->description }}</td>
                                <td class="px-5 py-3.5 font-mono text-slate-500">#{{ $due->payment_reference ?? $due->reference_number ?? $due->due_id }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $due->academic_year }}</td>
                                <td class="px-5 py-3.5 font-bold text-slate-900 tabular-nums">GHS {{ number_format((float) $due->amount, 2) }}</td>
                                <td class="px-5 py-3.5 text-slate-500 tabular-nums">{{ optional($due->due_date)->format('M j, Y') ?? $due->due_date }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @php($status = $due->payment_status)
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-md px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider',
                                        'bg-rose-50 text-rose-700 border border-rose-200/50' => $status === 'owing',
                                        'bg-amber-50 text-amber-700 border border-amber-200/50' => $status === 'pending_verification',
                                        'bg-emerald-50 text-emerald-800 border border-emerald-200/50' => $status === 'paid',
                                        'bg-slate-100 text-slate-600 border border-slate-200/50' => ! in_array($status, ['owing', 'pending_verification', 'paid'], true),
                                    ])>
                                        <span @class([
                                            'h-1 w-1 rounded-full',
                                            'bg-rose-500' => $status === 'owing',
                                            'bg-amber-500 animate-pulse' => $status === 'pending_verification',
                                            'bg-emerald-600' => $status === 'paid',
                                            'bg-slate-500' => ! in_array($status, ['owing', 'pending_verification', 'paid'], true),
                                        ])></span>
                                        {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-50">
                                            <i class="ri-inbox-line text-xl text-slate-300"></i>
                                        </span>
                                        <p class="text-sm font-semibold text-slate-500">No dues recorded</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-layouts.admin>
