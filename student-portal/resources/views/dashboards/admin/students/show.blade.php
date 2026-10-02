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
                        <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                            <span>Edit credentials</span>
                            <i class="ri-pencil-line text-sm"></i>
                        </a>
                    </div>
                </div>
            </article>
        </section>

    </div>
</x-layouts.admin>
