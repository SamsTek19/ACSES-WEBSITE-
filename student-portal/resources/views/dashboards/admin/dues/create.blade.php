@php($title = 'Create academic year due')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-5xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dues.index') }}" class="hover:text-[#0b3019] transition-colors">Dues</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Issue new due</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Configure dues for the academic year</h1>
                <p class="text-sm text-slate-500">Set the base amount and fine-tune class/year variations. All existing and future students will inherit these dues automatically.</p>
            </div>
            <a href="{{ route('admin.dues.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back to dues
            </a>
        </header>

        <form method="POST" action="{{ route('admin.dues.store') }}" class="space-y-5">
            @csrf

            <!-- Due details -->
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Due details</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="description" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</label>
                        <input id="description" type="text" name="description" value="{{ old('description') }}" required maxlength="255" placeholder="Departmental dues 2025" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @error('description')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="academic_year" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic year</label>
                        <div class="relative">
                            <input id="academic_year" type="text" name="academic_year" value="{{ old('academic_year') }}" required placeholder="2025/2026" pattern="^\d{4}/\d{4}$" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 pr-20 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">YYYY/YYYY</span>
                        </div>
                        @error('academic_year')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label for="due_date" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Due date</label>
                        <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @error('due_date')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="base_amount" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Base amount (GHS)</label>
                        <input id="base_amount" type="number" step="0.01" min="0" name="base_amount" value="{{ old('base_amount', '0.00') }}" required class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        <span class="text-[10px] text-slate-400">Applied wherever class/year overrides are blank.</span>
                        @error('base_amount')
                            <span class="text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Class & year matrix -->
            <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">
                <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                    <h2 class="text-sm font-bold text-slate-900">Class &amp; year matrix</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Override the base amount for specific class/year cohorts. Leave blank to inherit the base amount.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Class / Year</th>
                                @foreach ($matrix['years'] as $year)
                                    <th class="px-5 py-3 text-center">Year {{ $year }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($matrix['classes'] as $class)
                                <tr class="hover:bg-slate-50/40 transition">
                                    <th scope="row" class="whitespace-nowrap px-5 py-3 text-xs font-semibold text-slate-700">{{ $class }}</th>
                                    @foreach ($matrix['years'] as $year)
                                        <td class="px-4 py-2.5">
                                            <div class="relative">
                                                <input type="number" step="0.01" min="0" name="amounts[{{ $class }}][{{ $year }}]" value="{{ old("amounts.$class.$year", $matrix['values'][$class][$year] ?? '') }}" placeholder="Base" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 pr-10 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">GHS</span>
                                            </div>
                                            @error("amounts.$class.$year")
                                                <span class="text-xs text-rose-600">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between animate-fade-slide animate-fade-slide-delay-200">
                <p class="text-xs text-slate-500">All current students will receive this due immediately. New student accounts inherit active dues automatically.</p>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.dues.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Cancel</a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-send-plane-line text-sm"></i>
                        Issue due
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
