@php
    use Illuminate\Support\Str;
    $title = 'Announcements';
@endphp

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
        <div class="space-y-6">

            <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <i class="ri-megaphone-fill text-sm" aria-hidden="true"></i>
                        <span>Admin communications</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Announcements</h1>
                    <p class="text-sm text-slate-500">Broadcast updates to the entire student body or target specific groups.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('admin.announcements.create') }}" class="h-9 flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95" aria-label="Create new announcement">
                        <i class="ri-add-line text-sm" aria-hidden="true"></i>
                        New announcement
                    </a>
                </div>
            </header>

            <section class="space-y-0 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">

                {{-- Filter bar --}}
                <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                    <form method="GET" class="grid gap-3 md:grid-cols-4 md:gap-4">
                        <label class="flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search</span>
                            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Title or content" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20" />
                        </label>

                        <label class="flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Type</span>
                            <div class="relative">
                                <select name="type" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="">All types</option>
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </label>

                        <label class="flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Priority</span>
                            <div class="relative">
                                <select name="priority" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="">All priorities</option>
                                    @foreach ($priorities as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </label>

                        <label class="flex flex-col gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Audience</span>
                            <div class="relative">
                                <select name="target_type" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/20">
                                    <option value="">All audiences</option>
                                    @foreach ($targetTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['target_type'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </label>

                        <div class="md:col-span-4 flex items-center justify-end gap-2">
                            <a href="{{ route('admin.announcements.index') }}" class="h-8 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                                Reset
                            </a>
                            <button type="submit" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                                <i class="ri-filter-3-line text-sm"></i>
                                Apply filters
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Count + rows-per-page bar --}}
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs font-semibold text-slate-600">
                        Showing {{ $announcements->firstItem() ?? 0 }}–{{ $announcements->lastItem() ?? 0 }} of {{ $announcements->total() }} announcements
                    </p>
                    <form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
                        @foreach (request()->except(['per_page', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="announcements_per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
                        <select id="announcements_per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" x-on:change="$el.form.submit()">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Desktop table --}}
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Announcement</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Priority</th>
                                <th scope="col" class="px-6 py-3">Audience</th>
                                <th scope="col" class="px-6 py-3">Sent</th>
                                <th scope="col" class="px-6 py-3 text-right">Delivered</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($announcements as $announcement)
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-3.5">
                                        <div class="flex flex-col gap-1 max-w-xs">
                                            <span class="text-sm font-semibold text-slate-900 leading-tight">{{ $announcement->title }}</span>
                                            <p class="text-xs text-slate-400 line-clamp-2">{{ $announcement->excerpt ?? Str::limit(strip_tags($announcement->content), 100) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs font-medium text-slate-500">{{ $types[$announcement->type] ?? Str::headline($announcement->type) }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ match($announcement->priority) {
                                            'high' => 'bg-rose-50 text-rose-600',
                                            'low'  => 'bg-sky-50 text-sky-700',
                                            default => 'bg-emerald-50 text-emerald-700',
                                        } }}">
                                            {{ $priorities[$announcement->priority] ?? Str::headline($announcement->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-500">{{ $targetTypes[$announcement->target_type] ?? Str::headline($announcement->target_type) }}</td>
                                    <td class="px-6 py-3.5 text-xs text-slate-400 tabular-nums">{{ $announcement->sent_at?->format('M j, Y · g:i A') ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-right text-xs font-semibold tabular-nums text-slate-600">{{ number_format($announcement->delivered_count ?? 0) }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                                <i class="ri-edit-line" aria-hidden="true"></i>
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                    <i class="ri-delete-bin-line" aria-hidden="true"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                                <i class="ri-megaphone-off-line text-2xl text-slate-300"></i>
                                            </span>
                                            <p class="text-sm font-semibold text-slate-600">No announcements yet</p>
                                            <p class="text-xs text-slate-400">Send your first update using the "New announcement" button above.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse ($announcements as $announcement)
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $announcement->title }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">{{ $announcement->sent_at?->format('M j, Y · g:i A') ?? 'Pending dispatch' }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ match($announcement->priority) {
                                    'high' => 'bg-rose-50 text-rose-600',
                                    'low'  => 'bg-sky-50 text-sky-700',
                                    default => 'bg-emerald-50 text-emerald-700',
                                } }}">
                                    {{ $priorities[$announcement->priority] ?? Str::headline($announcement->priority) }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 line-clamp-2">{{ $announcement->excerpt ?? Str::limit(strip_tags($announcement->content), 120) }}</p>
                            <dl class="mt-3 grid grid-cols-2 gap-y-1.5 text-xs text-slate-500">
                                <div><dt class="text-slate-400">Type</dt><dd class="font-medium">{{ $types[$announcement->type] ?? Str::headline($announcement->type) }}</dd></div>
                                <div><dt class="text-slate-400">Audience</dt><dd class="font-medium">{{ $targetTypes[$announcement->target_type] ?? Str::headline($announcement->target_type) }}</dd></div>
                                <div><dt class="text-slate-400">Delivered</dt><dd class="font-semibold tabular-nums">{{ number_format($announcement->delivered_count ?? 0) }}</dd></div>
                            </dl>
                            <div class="mt-3 flex items-center gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                    <i class="ri-edit-line" aria-hidden="true"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                        <i class="ri-delete-bin-line" aria-hidden="true"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center">
                            <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                                <i class="ri-megaphone-off-line text-2xl text-slate-300"></i>
                            </span>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No announcements yet</p>
                            <p class="text-xs text-slate-400">Send your first update using the button above.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="text-xs text-slate-400">Page {{ $announcements->currentPage() }} of {{ $announcements->lastPage() }}</p>
                    <div class="sm:ml-auto flex justify-center sm:justify-end">
                        {{ $announcements->onEachSide(1)->links('vendor.pagination.data-limit') }}
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>
