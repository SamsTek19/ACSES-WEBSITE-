<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    <div class="mx-auto w-full max-w-[1600px] space-y-10 px-5 py-12 sm:px-6 lg:px-8">
        <div class="space-y-10">
            <section class="hidden sm:block animate-fade-slide overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)]">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-100">Updates</span>
                        <div class="space-y-2">
                            <h1 class="text-3xl font-semibold md:text-4xl">Campus announcements</h1>
                            <p class="max-w-2xl text-sm text-emerald-100/85">
                                Stay informed about academic deadlines, maintenance windows, security advisories, and key ACSES portal changes.
                            </p>
                        </div>
                    </div>
                    
                </div>
            </section>

            <form method="GET" action="{{ route('student.announcements.index') }}" 
                  x-data="{ showFilters: false }" 
                  class="rounded-[20px] border border-slate-100 bg-slate-50/50 p-4 sm:p-6 space-y-4">
                
                <!-- Main Search Row -->
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <i class="ri-search-line text-base"></i>
                        </span>
                        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Search announcements..." 
                               class="w-full rounded-[16px] border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                    </div>
                    <div class="flex gap-2">
                        <!-- Mobile filter toggle button -->
                        <button type="button" 
                                @click="showFilters = !showFilters" 
                                :class="showFilters ? 'bg-[#0b3019]/10 text-[#0b3019] border-[#0b3019]/25' : 'bg-white text-slate-600 border-slate-200'"
                                class="inline-flex items-center justify-center gap-2 rounded-[16px] border px-4 py-3 text-sm font-semibold transition sm:hidden hover:bg-slate-50">
                            <i class="ri-filter-3-line"></i>
                            <span>Filters</span>
                        </button>
                        <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-[16px] bg-[#0b3019] px-6 py-3 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#094018]">
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Collapsible Advanced Filters (Mobile Only) -->
                <div x-show="showFilters" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid gap-3 pt-3 border-t border-slate-200/60 sm:hidden">
                    
                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Type</span>
                        <div class="relative">
                            <select name="type" class="w-full appearance-none rounded-[16px] border border-slate-200 bg-white py-2.5 pl-4 pr-12 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                <option value="">All Types</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['type'] === $value)> {{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                        </div>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Priority</span>
                        <div class="relative">
                            <select name="priority" class="w-full appearance-none rounded-[16px] border border-slate-200 bg-white py-2.5 pl-4 pr-12 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                <option value="">All Priorities</option>
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['priority'] === $value)> {{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                        </div>
                    </label>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center rounded-full bg-[#0b3019] py-2.5 text-xs font-bold uppercase tracking-[0.1em] text-white">
                            Apply Advanced
                        </button>
                        <a href="{{ route('student.announcements.index') }}" class="flex-1 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white py-2.5 text-xs font-bold uppercase tracking-[0.1em] text-slate-600">
                            Reset
                        </a>
                    </div>
                </div>

                <!-- Desktop Grid (Hidden on Mobile) -->
                <div class="hidden sm:grid sm:grid-cols-3 gap-4">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Type</span>
                        <div class="relative">
                            <select name="type" class="w-full appearance-none rounded-[16px] border border-slate-200 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                <option value="">All Types</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['type'] === $value)> {{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                        </div>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Priority</span>
                        <div class="relative">
                            <select name="priority" class="w-full appearance-none rounded-[16px] border border-slate-200 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                <option value="">All Priorities</option>
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['priority'] === $value)> {{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                        </div>
                    </label>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#0b3019] px-4 py-3.5 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#094018]">
                            <i class="ri-search-line"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('student.announcements.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Reset</a>
                    </div>
                </div>
            </form>

            <section class="space-y-4">
                <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0b3019]">All announcements</h2>
                        <p class="text-sm text-slate-500">Sorted by most recent first. Use filters above to narrow down the results.</p>
                    </div>
                    @if ($announcements->hasPages())
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <span>Page {{ $announcements->currentPage() }} of {{ $announcements->lastPage() }}</span>
                            {{ $announcements->onEachSide(1)->links('vendor.pagination.simple-tailwind') }}
                        </div>
                    @endif
                </header>

                @if ($announcements->isEmpty())
                    <article class="rounded-[24px] border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500">
                        <p>No announcements match your filters right now. Try clearing the filters or check back later.</p>
                    </article>
                @else
                    <div class="space-y-5">
                        @foreach ($announcements as $announcement)
                            <article class="group flex flex-col rounded-[24px] border border-slate-200/80 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-[#0b3019]/10">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">{{ $types[$announcement->type] ?? Str::headline($announcement->type) }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">{{ $priorities[$announcement->priority] ?? Str::headline($announcement->priority) }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900">{{ $announcement->title }}</h3>
                                    <p class="text-sm text-slate-600 line-clamp-2">{{ $announcement->excerpt ?? Str::limit(strip_tags($announcement->content), 140) }}</p>
                                </div>
                                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-5 text-xs font-medium text-slate-500">
                                    <div class="flex items-center gap-4">
                                        <span class="flex items-center gap-1.5"><i class="ri-calendar-line"></i> {{ $announcement->published_at?->format('M j, Y') }}</span>
                                        <span class="flex items-center gap-1.5"><i class="ri-time-line"></i> {{ $announcement->published_at?->format('g:i A') }}</span>
                                    </div>
                                    <a href="{{ route('student.announcements.show', $announcement) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#0b3019] transition hover:text-[#0b3019]/80">
                                        Read more
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        {{ $announcements->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.dashboard>
