{{--
    Manual Payment Modal Partial
    Expects: $due (Due model), $paymentSettings (array)
    Usage: @include('dashboards.student.dues._manual-pay-modal', ['due' => $due])
--}}
<x-modal name="manual-pay-{{ $due->due_id }}" focusable>
    <form
        action="{{ route('student.payments.manual.submit', $due) }}"
        method="POST"
        enctype="multipart/form-data"
        class="p-6"
    >
        @csrf

        {{-- Header --}}
        <div class="mb-5">
            <h2 class="text-base font-bold text-slate-900">Manual Payment</h2>
            <p class="mt-0.5 text-sm text-slate-500">
                Submit your payment proof for
                <span class="font-semibold text-slate-800">{{ $due->description }}</span>
                &mdash; GHS {{ number_format((float)$due->amount, 2) }}.
            </p>
        </div>

        {{-- Payment Details --}}
        <div class="grid gap-3 sm:grid-cols-2">
            {{-- Bank Details --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Bank Transfer</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Bank</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $paymentSettings['bank_name'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Account Name</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $paymentSettings['account_name'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Account No.</span>
                        <span class="font-bold tracking-widest text-slate-900 text-right">{{ $paymentSettings['account_number'] ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Mobile Money --}}
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-emerald-500">Mobile Money</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="text-emerald-600">Name</span>
                        <span class="font-semibold text-emerald-900 text-right">{{ $paymentSettings['momo_name'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-emerald-600">Number</span>
                        <span class="font-bold tracking-widest text-emerald-900 text-right">{{ $paymentSettings['momo_number'] ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instructions --}}
        @if (!empty($paymentSettings['instructions']))
            <div class="mt-3 flex items-start gap-2 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                <i class="ri-information-line mt-0.5 shrink-0 text-sm text-amber-500"></i>
                <p>{{ $paymentSettings['instructions'] }}</p>
            </div>
        @endif

        {{-- Form Fields --}}
        <div class="mt-5 space-y-4">
            {{-- Receipt Upload --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700">
                    Payment Receipt <span class="text-red-500">*</span>
                </label>
                <p class="mt-0.5 text-xs text-slate-400">Upload a clear image of your transfer receipt (JPG, PNG, GIF).</p>
                <input
                    type="file"
                    name="receipt"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    required
                    class="mt-2 block w-full cursor-pointer rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-500
                           file:mr-3 file:border-0 file:rounded-md file:bg-[#0b3019] file:px-3 file:py-1.5
                           file:text-xs file:font-semibold file:text-white hover:file:bg-[#0b3019]/85"
                >
            </div>

            {{-- Transaction Reference --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700">Transaction ID / Reference <span class="text-slate-400 font-normal">(Optional)</span></label>
                <input
                    type="text"
                    name="reference"
                    maxlength="100"
                    placeholder="e.g. 1598234760"
                    class="mt-1.5 h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 placeholder-slate-400 transition focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0b3019]/15"
                >
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="mt-6 flex justify-end gap-2">
            <button
                type="button"
                @click="$dispatch('close')"
                class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-[0.98]"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-sm font-semibold text-white transition hover:bg-[#0b3019]/90 active:scale-[0.98]"
            >
                <i class="ri-upload-2-line text-sm"></i>
                Submit Proof
            </button>
        </div>
    </form>
</x-modal>
