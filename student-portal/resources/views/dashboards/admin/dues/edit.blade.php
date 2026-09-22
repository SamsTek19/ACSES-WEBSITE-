@php($title = 'Edit due · ' . ($due->student->fullname ?? $due->student->username ?? 'Student #' . $due->student_id))

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-5xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dues.index') }}" class="hover:text-[#0b3019] transition-colors">Dues</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Edit due</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $due->description }}</h1>
                <p class="text-sm text-slate-500">Adjust billing details for the selected student. Changes take effect immediately.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm shrink-0">
                <p class="font-semibold text-slate-900">{{ $due->student->fullname ?? $due->student->username ?? 'Student #' . $due->student_id }}</p>
                <p class="text-xs mt-0.5">{{ $due->student->email ?? 'No email on file' }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $due->student->class ?? '—' }} · {{ $due->student->year ? 'Year ' . $due->student->year : '—' }}</p>
            </div>
        </header>

        <form method="POST" action="{{ route('admin.dues.update', $due) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900">Due details</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</span>
                        <input type="text" name="description" value="{{ old('description', $due->description) }}" maxlength="255" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('description')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic year</span>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $due->academic_year) }}" pattern="^\d{4}/\d{4}$" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('academic_year')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <label class="flex flex-col gap-1.5 md:col-span-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Due date</span>
                        <input type="date" name="due_date" value="{{ old('due_date', optional($due->due_date)->format('Y-m-d')) }}" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('due_date')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Amount (GHS)</span>
                        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount', $due->amount) }}" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('amount')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
            </section>

            <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900">Payment status</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</span>
                        <div class="relative">
                            <select name="payment_status" required class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_status', $due->payment_status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('payment_status')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-600 mt-5">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" {{ old('is_active', $due->is_active) ? 'checked' : '' }}>
                        <span>Mark due as active</span>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Payment method</span>
                        <input type="text" name="payment_method" value="{{ old('payment_method', $due->payment_method) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('payment_method')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Payment reference</span>
                        <input type="text" name="payment_reference" value="{{ old('payment_reference', $due->payment_reference) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('payment_reference')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Payment date</span>
                        <input type="datetime-local" name="payment_date" value="{{ old('payment_date', optional($due->payment_date)->format('Y-m-d\TH:i')) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('payment_date')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Verification date</span>
                        <input type="datetime-local" name="verification_date" value="{{ old('verification_date', optional($due->verification_date)->format('Y-m-d\TH:i')) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('verification_date')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Verification notes</span>
                    <textarea name="verification_notes" rows="3" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">{{ old('verification_notes', $due->verification_notes) }}</textarea>
                    @error('verification_notes')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
            </section>

            <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900">Additional metadata</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Network</span>
                        <input type="text" name="network" value="{{ old('network', $due->network) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('network')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Reference number</span>
                        <input type="text" name="reference_number" value="{{ old('reference_number', $due->reference_number) }}" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                        @error('reference_number')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Payment notes</span>
                    <textarea name="payment_notes" rows="3" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">{{ old('payment_notes', $due->payment_notes) }}</textarea>
                    @error('payment_notes')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
                <label class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rejection reason</span>
                    <textarea name="rejection_reason" rows="3" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">{{ old('rejection_reason', $due->rejection_reason) }}</textarea>
                    @error('rejection_reason')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
            </section>

            <div class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">Changes save immediately and will update the student's financial overview.</p>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.dues.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Cancel</a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-save-line text-sm" aria-hidden="true"></i>
                        Save changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
