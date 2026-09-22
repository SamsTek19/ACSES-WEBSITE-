@php($title = $title ?? 'Manage academic resources')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl space-y-10 px-5 py-10 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-4 rounded-3xl border border-[#0b3019]/15 bg-white/80 p-6 text-center shadow-lg shadow-[#0b3019]/5 sm:text-left md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-[#0b3019]">
                    <i class="ri-book-3-line text-base" aria-hidden="true"></i>
                    Academic resources
                </p>
                <h1 class="text-2xl font-semibold text-[#0b3019] md:text-3xl">Curate resources for every cohort</h1>
                <p class="text-sm text-slate-600">Upload lecture materials, past questions, and helpful links with clear targeting by class and year.</p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 md:justify-end">
                <a href="{{ route('admin.resources.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#0b3019] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#0b3019]/20 transition hover:-translate-y-0.5 hover:shadow-xl" aria-label="Create new academic resource">
                    <i class="ri-add-line text-base" aria-hidden="true"></i>
                    New resource
                </a>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-3xl border border-emerald-200/60 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-inner">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="ri-check-line text-lg" aria-hidden="true"></i>
                    </span>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <section class="space-y-6 rounded-3xl border border-[#0b3019]/10 bg-white p-6 shadow-lg shadow-[#0b3019]/10">
            <div class="flex flex-col gap-4 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <div>
                    <h2 class="text-lg font-semibold text-[#0b3019]">Resource library</h2>
                    <p class="text-sm text-slate-500">Showing {{ $resources->firstItem() ?? 0 }}-{{ $resources->lastItem() ?? 0 }} of {{ $resources->total() }} resources.</p>
                </div>
                <form method="GET" class="flex flex-col items-center gap-2 sm:flex-row" x-data>
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="text-sm font-medium text-slate-600">Rows per page</label>
                    <select id="per_page" name="per_page" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#0b3019] focus:ring-[#0b3019] sm:w-auto" x-on:change="$el.form.submit()">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200/70">
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Resource</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Audience</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @if ($resources->count())
                                @include('dashboards.admin.resources.partials.table-rows', ['resources' => $resources])
                            @else
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <i class="ri-book-mark-line text-3xl text-slate-300"></i>
                                            <p class="font-semibold text-slate-600">No resources yet.</p>
                                            <p>Upload your first resource to make materials available to students.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="grid gap-4 md:hidden">
                    @if ($resources->count())
                        @include('dashboards.admin.resources.partials.mobile-list', ['resources' => $resources])
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 p-10 text-center text-sm text-slate-500">
                            <i class="ri-book-mark-line text-3xl text-slate-300"></i>
                            <p class="mt-3 font-semibold text-slate-600">No resources yet.</p>
                            <p>Upload your first resource to make materials available to students.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-slate-200/70 pt-4 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs text-slate-500">Page {{ $resources->currentPage() }} of {{ $resources->lastPage() }}</p>
                <div class="sm:ml-auto">
                    {{ $resources->appends(['per_page' => $currentPerPage])->onEachSide(1)->links() }}
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
