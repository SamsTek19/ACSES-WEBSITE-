<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')
    @php
        $categories = $categories ?? collect();
    @endphp

    <div class="mx-auto w-full max-w-[1600px] space-y-10 px-4 py-12 sm:px-6 lg:px-8">

        <div class="space-y-10">
            <section class="hidden sm:block animate-fade-slide overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)]">
                <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-100">Campus life</span>
                        <div class="space-y-2">
                            <h1 class="text-3xl font-semibold md:text-4xl">Upcoming events</h1>
                            <p class="max-w-2xl text-sm text-emerald-100/85">
                                Stay on top of ACSES seminars, workshops, and social meetups. Search or filter by category to plan your week ahead.
                            </p>
                        </div>
                    </div>
               
                </div>
            </section>

            <form method="GET" action="{{ route('student.events.index') }}" class="grid gap-4 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/10 lg:grid-cols-[1fr_auto]">
                <div class="flex flex-col gap-2">
                    <label for="search" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Search events</label>
                    <div class="relative">
                        <input id="search" name="search" type="search" value="{{ $search }}" placeholder="e.g. robotics workshop" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pl-10 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M10 18a8 8 0 1 1 8-8" />
                            <path d="m22 22-4.35-4.35" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#0b3019] px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#094018] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                        Apply filters
                    </button>
                    <a href="{{ route('student.events.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Reset</a>
                </div>
                @if ($categories->isNotEmpty())
                    <div class="lg:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Categories</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <a href="{{ route('student.events.index', array_filter(['search' => $search])) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-semibold {{ $activeCategory === '' ? 'border-[#0b3019] bg-[#0b3019]/10 text-[#0b3019]' : 'border-slate-200 text-slate-500 hover:border-[#0b3019]/40 hover:text-[#0b3019]' }}">
                                All events
                            </a>
                            @foreach ($categories as $category)
                                <a href="{{ route('student.events.index', array_filter(['category' => $category['slug'], 'search' => $search])) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-semibold {{ $activeCategory === $category['slug'] ? 'border-[#0b3019] bg-[#0b3019]/10 text-[#0b3019]' : 'border-slate-200 text-slate-500 hover:border-[#0b3019]/40 hover:text-[#0b3019]' }}">
                                    {{ $category['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </form>

            @if ($events->isEmpty())
                <section class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-12 text-center text-sm text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M8 7h8" />
                        <path d="M8 11h5" />
                        <path d="M4 5h16v14H4z" />
                    </svg>
                    <p class="mt-4 font-semibold text-slate-600">No upcoming events match your filters.</p>
                    <p class="mt-2">Check back later or adjust your search.</p>
                </section>
            @else
                <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($events as $event)
                        <article class="flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-lg shadow-[#0b3019]/5 transition hover:-translate-y-1 hover:shadow-[#0b3019]/15">
                            <div class="relative aspect-[16/9] w-full bg-slate-100">
                                @if (!empty($event['banner_url']))
                                    <img
                                        src="{{ $event['banner_url'] }}"
                                        alt="{{ $event['banner_alt'] ?? ($event['title'] . ' banner') }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#0b3019]/5 via-slate-100 to-[#0b3019]/5">
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0b3019]/10 text-[#0b3019]">
                                            <i class="ri-image-line text-2xl" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-12 w-12 flex-col items-center justify-center rounded-2xl border border-[#0b3019]/20 bg-[#0b3019]/5 text-sm font-semibold text-[#0b3019]">
                                            <span>{{ optional($event['start_at'])->format('M') }}</span>
                                            <span class="text-lg">{{ optional($event['start_at'])->format('d') }}</span>
                                        </span>
                                        <div>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]/80">
                                                <i class="fa-solid fa-calendar-star text-[#0b3019]"></i>
                                                {{ $event['category'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <h2 class="text-lg font-semibold text-slate-900">{{ $event['title'] }}</h2>
                                        <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($event['description'] ?? ''), 160) }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3 text-sm text-slate-500">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3019]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path d="M8 7V3" />
                                            <path d="M16 7V3" />
                                            <path d="M4 11h16" />
                                            <path d="M5 5h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1Z" />
                                        </svg>
                                        <span>{{ optional($event['start_at'])->format('D · M j, Y · g:i A') }}</span>
                                    </div>
                                    @if ($event['end_at'])
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3019]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path d="M8 7V3" />
                                                <path d="M16 7V3" />
                                                <path d="M4 11h16" />
                                                <path d="M5 5h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1Z" />
                                            </svg>
                                            <span>{{ optional($event['end_at'])->format('D · M j, Y · g:i A') }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($event['cta_url'])
                                    <a href="{{ $event['cta_url'] }}" target="{{ \Illuminate\Support\Str::startsWith($event['cta_url'], ['http://', 'https://']) ? '_blank' : '_self' }}" rel="noopener" class="mt-6 mb-6 ml-auto inline-flex items-center gap-2 px-6 text-sm font-semibold text-[#0b3019] transition hover:underline">
                                        Learn more
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
