@php($title = $title ?? 'Manage events')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
        <div class="space-y-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <i class="ri-calendar-check-fill text-sm" aria-hidden="true"></i>
                    <span>Event management</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Plan &amp; publish events</h1>
                <p class="text-sm text-slate-500">Create and curate campus events that appear on the student timeline.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('admin.events.create') }}" class="h-9 flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95" aria-label="Create new event">
                    <i class="ri-add-line text-sm" aria-hidden="true"></i>
                    New event
                </a>
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
            <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs font-semibold text-slate-600">
                    Showing {{ $events->firstItem() ?? 0 }}–{{ $events->lastItem() ?? 0 }} of {{ $events->total() }} events
                </p>
                <form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                    <select id="per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" x-on:change="$el.form.submit()">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-hidden">
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Event</th>
                                <th scope="col" class="px-6 py-3">Schedule</th>
                                <th scope="col" class="px-6 py-3">Location</th>
                                <th scope="col" class="px-6 py-3">Category</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @include('dashboards.admin.events.partials.table-rows', ['events' => $events])
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden">
                    @include('dashboards.admin.events.partials.mobile-list', ['events' => $events])
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs text-slate-400">Page {{ $events->currentPage() }} of {{ $events->lastPage() }}</p>
                <div class="sm:ml-auto flex justify-center sm:justify-end">
                    {{ $events->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
