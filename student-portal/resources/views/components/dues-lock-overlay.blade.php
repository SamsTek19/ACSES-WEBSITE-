@php
    use App\Models\Due;

    $student = auth()->guard('student')->user();
    $isStudent = !is_null($student);

    $outstandingDuesCount = 0;
    $totalAmountOwed = 0;
    $dueItems = collect();

    if ($isStudent) {
        $dueItems = Due::query()
            ->where('student_id', $student->user_id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('payment_status', 'owing')
                  ->orWhere(function ($sq) {
                      $sq->where('payment_status', 'pending_verification')
                         ->whereNotIn('payment_method', ['paystack', 'rushpay']);
                  });
            })
            ->get();

        $outstandingDuesCount = $dueItems->count();
        $totalAmountOwed = (float) $dueItems->sum('amount');
    }

    // Exempt routes: dues, payments, profile — student must always be able to pay
    $isExemptRoute = request()->routeIs([
        'student.dues.*',
        'student.payments.*',
        'student.profile*',
        'auth.logout',
    ]);

    $shouldLockPage = $isStudent && $outstandingDuesCount > 0 && !$isExemptRoute;
@endphp

{{-- ─── Global Outstanding Dues Alert Bar ─────────────────────────────────── --}}
@if ($isStudent && $outstandingDuesCount > 0)
<div class="w-full border-b border-[#0b3019]/15 bg-[#0b3019]/5 px-4 py-2.5">
    <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0b3019]/10 text-[#0b3019]">
                <i class="ri-alarm-warning-line text-sm"></i>
            </span>
            <p class="text-sm text-[#0b3019]">
                <span class="font-semibold">Outstanding Dues:</span>
                You have <strong>{{ $outstandingDuesCount }} unpaid due{{ $outstandingDuesCount > 1 ? 's' : '' }}</strong>
                totaling <strong>GHS {{ number_format($totalAmountOwed, 2) }}</strong>. Please clear your balance.
            </p>
        </div>
        <a href="{{ route('student.dues.index') }}"
           class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-[#0b3019]/25 bg-white px-3 py-1.5 text-xs font-semibold text-[#0b3019] shadow-sm transition hover:bg-[#0b3019]/5 active:scale-95">
            <i class="ri-bank-card-line text-sm"></i>
            Pay Now
        </a>
    </div>
</div>
@endif

{{-- ─── Blur Lock Wrapper ──────────────────────────────────────────────────── --}}
@if ($shouldLockPage)
<div class="relative w-full overflow-hidden">

    {{-- Blurred background content --}}
    <div class="pointer-events-none select-none blur-sm opacity-30 max-h-[70vh] overflow-hidden"
         aria-hidden="true">
        {{ $slot }}
    </div>

    {{-- Lock Overlay Card --}}
    <div class="absolute inset-0 z-30 flex items-start justify-center px-4 pt-16 sm:pt-24">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white shadow-xl">

            {{-- Header --}}
            <div class="flex items-center gap-4 border-b border-slate-100 px-6 py-5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="ri-lock-line text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Access Restricted</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Clear your outstanding dues to unlock this page.</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Amount Summary --}}
                <div class="flex items-center justify-between rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <span class="text-sm text-slate-600 font-medium">Total Outstanding</span>
                    <span class="text-lg font-bold text-slate-900">GHS {{ number_format($totalAmountOwed, 2) }}</span>
                </div>

                {{-- Breakdown list --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Breakdown</p>
                    <div class="divide-y divide-slate-50 rounded-xl border border-slate-100 bg-white overflow-hidden max-h-40 overflow-y-auto">
                        @foreach ($dueItems as $item)
                            <div class="flex items-center justify-between px-4 py-2.5 text-sm">
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800 truncate">{{ $item->description }}</p>
                                    <p class="text-xs text-slate-400">{{ $item->academic_year }}</p>
                                </div>
                                <span class="ml-4 shrink-0 font-semibold text-slate-900">
                                    GHS {{ number_format((float)$item->amount, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-slate-100 px-6 py-4 flex flex-col gap-2.5">
                <a href="{{ route('student.dues.index') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0b3019] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0b3019]/90 active:scale-[0.98]">
                    <i class="ri-bank-card-line text-base"></i>
                    Pay Outstanding Dues
                </a>
                <p class="text-center text-xs text-slate-400">
                    Questions? <a href="mailto:acsesrepos@gmail.com" class="font-medium text-[#0b3019] hover:underline">Contact the Financial Secretary</a>
                </p>
            </div>

        </div>
    </div>

</div>
@else
    {{ $slot }}
@endif
