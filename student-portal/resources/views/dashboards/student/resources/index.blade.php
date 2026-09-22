<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    <div class="mx-auto w-full max-w-[1600px] px-5 py-12 sm:px-6 lg:px-8">

        <div class="space-y-10">
            <section class="hidden sm:block animate-fade-slide overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)] relative">
                <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between relative z-10">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-100">Academics</span>
                        <div class="space-y-2">
                            <h1 class="text-3xl font-semibold md:text-4xl">Academic resources</h1>
                            <p class="max-w-2xl text-sm text-emerald-100/85">
                                Explore lecture handouts, past questions, recordings, and course links curated by the academic office. Use the search tool to quickly locate what you need.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <form method="GET" action="{{ route('student.resources.index') }}" class="grid gap-4 rounded-[20px] border border-slate-100 bg-slate-50/50 p-6 lg:grid-cols-[1fr_auto]">
                <div class="flex flex-col gap-2">
                    <label for="search" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Search library</label>
                    <div class="relative">
                        <input id="search" name="search" type="search" value="{{ $search }}" placeholder="e.g. algorithms handout, level 200" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pl-10 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
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
                    <a href="{{ route('student.resources.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Reset</a>
                </div>
            </form>

            @if ($resources->isEmpty())
                <section class="rounded-[24px] border border-dashed border-slate-300 bg-white/70 p-12 text-center text-sm text-slate-500">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100/50 mb-4">
                        <i class="ri-folder-open-line text-3xl text-slate-400"></i>
                    </div>
                    <p class="mt-4 font-semibold text-slate-600">No academic resources match your filters yet.</p>
                    <p class="mt-2">Try a different keyword or browse another category.</p>
                </section>
            @else
                <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($resources as $resource)
                        <article class="group flex h-full flex-col justify-between rounded-[24px] border border-slate-200/80 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-[#0b3019]/10">
                            <div class="space-y-3">
                                <span class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]/80">
                                    <i class="fa-solid {{ $resource['badge_icon'] ?? 'fa-book-open-reader' }} text-[#0b3019]"></i>
                                    {{ $resource['badge_label'] ?? 'Resource' }}
                                </span>
                                <h2 class="text-lg font-semibold text-slate-900">{{ $resource['title'] }}</h2>
                                <p class="text-sm text-slate-600">{{ $resource['description'] }}</p>
                            </div>
                            @php
                                $target = ($resource['is_file'] ?? false) || \Illuminate\Support\Str::startsWith($resource['cta_url'], ['http://', 'https://']) ? '_blank' : '_self';
                            @endphp
                            <a href="{{ $resource['cta_url'] }}" target="{{ $target }}" rel="noopener" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[#0b3019] transition hover:underline">
                                {{ $resource['cta_label'] }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </a>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
