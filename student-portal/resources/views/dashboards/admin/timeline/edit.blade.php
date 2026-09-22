@php($title = $title ?? 'Edit timeline entry')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.timeline.index') }}" class="hover:text-[#0b3019] transition-colors">Timeline</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Edit milestone</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Refine academic timeline entry</h1>
                <p class="text-sm text-slate-500">Adjust messaging, dates, or CTA, then toggle visibility for students.</p>
            </div>
            <a href="{{ route('admin.timeline.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back
            </a>
        </header>

        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
            <form method="POST" action="{{ route('admin.timeline.update', $entry) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('dashboards.admin.timeline.partials.form', ['entry' => $entry])

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.timeline.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                        <i class="ri-arrow-go-back-line text-sm" aria-hidden="true"></i>
                        Back
                    </a>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-save-3-line text-sm" aria-hidden="true"></i>
                        Update milestone
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.timeline.destroy', $entry) }}" onsubmit="return confirm('Delete this timeline entry?');" class="flex justify-end mt-4 border-t border-slate-100 pt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-600 transition hover:border-rose-400 hover:bg-rose-100 active:scale-95">
                    <i class="ri-delete-bin-6-line text-sm" aria-hidden="true"></i>
                    Delete entry
                </button>
            </form>
        </section>
    </div>
</x-layouts.admin>
