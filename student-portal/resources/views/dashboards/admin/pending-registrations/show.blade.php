@php($title = $title ?? 'Registration Details')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        {{-- Header --}}
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.pending-registrations.index') }}" class="hover:text-[#0b3019] transition-colors">Pending registrations</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Details</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $registration->fullname }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold {{ match($registration->status) {
                        'approved' => 'bg-emerald-50 text-emerald-700',
                        'rejected' => 'bg-rose-50 text-rose-600',
                        default => 'bg-amber-50 text-amber-700',
                    } }}">
                        <i class="{{ match($registration->status) {
                            'approved' => 'ri-checkbox-circle-line',
                            'rejected' => 'ri-close-circle-line',
                            default => 'ri-time-line',
                        } }}"></i>
                        {{ Str::title($registration->status) }}
                    </span>
                    @if ($registration->reviewed_at)
                        <span class="text-xs text-slate-400">
                            Reviewed by {{ $registration->reviewer?->fullname ?? 'Admin' }} · {{ $registration->reviewed_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.pending-registrations.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back to registrations
            </a>
        </header>

        {{-- Alerts --}}
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <div class="flex items-center gap-2">
                    <i class="ri-check-double-line text-base text-emerald-600"></i>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <div class="flex items-center gap-2">
                    <i class="ri-error-warning-line text-base text-rose-500"></i>
                    <p>{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        {{-- Duplicate Warning --}}
        @if (! $canCreateUser['canCreate'])
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                <div class="flex items-start gap-3">
                    <i class="ri-alert-line text-lg" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Cannot create user account</p>
                        <ul class="mt-1 list-inside list-disc text-amber-600">
                            @foreach ($canCreateUser['issues'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Student Information --}}
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/40 px-6 py-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Student Information</h2>
            </div>
            <div class="p-6">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Full Name</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->fullname }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Username</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->username }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->email }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Phone Number</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->phone_number ?? 'Not provided' }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Reference Number</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->index_number }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Program</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->class }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Year</dt>
                        <dd class="text-base font-medium text-slate-900">Year {{ $registration->year }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Submitted On</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $registration->created_at->format('F j, Y \a\t g:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        {{-- Rejection Reason (if rejected) --}}
        @if ($registration->isRejected() && $registration->rejection_reason)
            <section class="rounded-2xl border border-rose-200/80 bg-rose-50/50 shadow-sm overflow-hidden">
                <div class="border-b border-rose-200/60 bg-rose-100/40 px-6 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-rose-600">Rejection Reason</h2>
                </div>
                <div class="p-6">
                    <p class="text-slate-700">{{ $registration->rejection_reason }}</p>
                </div>
            </section>
        @endif

        {{-- Actions --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm" x-data="{ rejectModalOpen: false }">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500">Actions</h2>
            <div class="flex flex-wrap gap-3">
                @if ($registration->isPending() || $registration->isRejected())
                    <form method="POST" action="{{ route('admin.pending-registrations.approve', $registration) }}" class="inline">
                        @csrf
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95" {{ ! $canCreateUser['canCreate'] ? 'disabled' : '' }}>
                            <i class="ri-checkbox-circle-line text-sm"></i>
                            {{ $registration->isRejected() ? 'Re-Approve' : 'Approve' }} registration
                        </button>
                    </form>
                @endif

                @if ($registration->isPending() || $registration->isApproved())
                    <button type="button" @click="rejectModalOpen = true" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700 active:scale-95">
                        <i class="ri-close-circle-line text-sm"></i>
                        {{ $registration->isApproved() ? 'Revoke & Reject' : 'Reject' }} registration
                    </button>
                @endif
            </div>

            {{-- Reject Modal --}}
            <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-transition.opacity>
                <div @click.outside="rejectModalOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <h3 class="text-base font-bold text-slate-900">Reject registration</h3>
                    <p class="mt-1 text-xs text-slate-500">Provide a reason for rejecting this registration. This will be sent to the student via email.</p>

                    <form method="POST" action="{{ route('admin.pending-registrations.reject', $registration) }}" class="mt-4">
                        @csrf
                        <div class="space-y-2">
                            <label for="rejection_reason" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rejection reason</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="Explain why this registration is being rejected...">{{ $registration->rejection_reason }}</textarea>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="button" @click="rejectModalOpen = false" class="flex-1 h-9 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 h-9 rounded-lg bg-rose-600 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">
                                Reject registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
