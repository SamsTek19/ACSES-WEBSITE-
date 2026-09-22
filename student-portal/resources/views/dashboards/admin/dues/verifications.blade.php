<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dues.index') }}" class="hover:text-[#0b3019] transition-colors">Dues</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Manual verifications</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pending manual verifications</h1>
                <p class="text-sm text-slate-500">Review receipts submitted by students for manual bank or Momo transfers.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
                <div class="flex items-center gap-2">
                    <i class="ri-check-double-line text-base text-emerald-600" aria-hidden="true"></i>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <section class="space-y-0 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Student</th>
                            <th scope="col" class="px-6 py-3">Due details</th>
                            <th scope="col" class="px-6 py-3">Submitted</th>
                            <th scope="col" class="px-6 py-3">Reference</th>
                            <th scope="col" class="px-6 py-3 text-center">Receipt</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($pendingDues as $due)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.students.show', $due->student_id) }}" class="group block">
                                        <p class="text-sm font-semibold text-slate-900 group-hover:text-[#0b3019] transition-colors">{{ $due->student->fullname }}</p>
                                        <p class="text-xs text-slate-400">{{ $due->student->index_number }}</p>
                                    </a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-sm font-medium text-slate-700">{{ $due->description }}</p>
                                    <p class="text-xs font-bold text-emerald-600 tabular-nums">GHS {{ number_format($due->amount, 2) }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-xs text-slate-500 tabular-nums">{{ $due->updated_at->format('M j, Y') }}</p>
                                    <p class="text-[10px] text-slate-400 tabular-nums">{{ $due->updated_at->format('g:i A') }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $due->payment_reference ?? 'N/A' }}</code>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @if($due->receipt_path)
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'view-receipt-{{ $due->due_id }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100"
                                        >
                                            <i class="ri-image-line text-base"></i>
                                        </button>

                                        <x-modal name="view-receipt-{{ $due->due_id }}" focusable>
                                            <div class="p-6">
                                                <div class="mb-4 flex items-center justify-between">
                                                    <h2 class="text-base font-bold text-slate-900">Receipt verification</h2>
                                                    <button @click="$dispatch('close')" class="text-slate-400 hover:text-slate-600"><i class="ri-close-line text-xl"></i></button>
                                                </div>
                                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                                    <img src="{{ asset('storage/' . $due->receipt_path) }}" alt="Receipt" class="w-full max-h-[70vh] object-contain bg-slate-100">
                                                </div>
                                                <div class="mt-4 flex justify-end">
                                                    <button @click="$dispatch('close')" class="h-9 rounded-lg bg-slate-100 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">Close</button>
                                                </div>
                                            </div>
                                        </x-modal>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.dues.verifications.approve', $due) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="h-8 inline-flex items-center gap-1 rounded-md bg-emerald-600 px-3 text-xs font-semibold text-white transition hover:bg-emerald-700 active:scale-95" onclick="return confirm('Confirm this payment as valid?')">
                                                <i class="ri-checkbox-circle-line"></i>
                                                Approve
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            @click="$dispatch('open-modal', 'reject-payment-{{ $due->due_id }}')"
                                            class="h-8 inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-100"
                                        >
                                            <i class="ri-close-circle-line"></i>
                                            Reject
                                        </button>

                                        <x-modal name="reject-payment-{{ $due->due_id }}" focusable>
                                            <form action="{{ route('admin.dues.verifications.reject', $due) }}" method="POST" class="p-6">
                                                @csrf
                                                <h2 class="text-base font-bold text-slate-900">Reject payment record</h2>
                                                <p class="mt-1 text-sm text-slate-500">Provide a reason so the student can fix it (e.g. "Blurred image" or "Reference mismatch").</p>
                                                <textarea name="rejection_reason" rows="3" class="mt-4 w-full rounded-lg border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500" placeholder="Type reason here…" required></textarea>
                                                <div class="mt-4 flex justify-end gap-2">
                                                    <button type="button" @click="$dispatch('close')" class="h-9 rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="h-9 rounded-lg bg-rose-600 px-4 text-sm font-semibold text-white transition hover:bg-rose-700">Submit</button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                            <i class="ri-verified-badge-line text-2xl text-slate-300"></i>
                                        </span>
                                        <p class="text-sm font-semibold text-slate-600">Queue is clear</p>
                                        <p class="text-xs text-slate-400">No pending manual payments require verification.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-3">
                {{ $pendingDues->links() }}
            </div>
        </section>
    </div>
</x-layouts.admin>
