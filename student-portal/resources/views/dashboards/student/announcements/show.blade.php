<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    <div class="mx-auto w-full max-w-[1000px] space-y-10 px-5 py-12 sm:px-6 lg:px-8">
        <div class="space-y-10">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('student.dashboard') }}" class="transition hover:text-[#0b3019]">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('student.announcements.index') }}" class="transition hover:text-[#0b3019]">Announcements</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-[#0b3019]">{{ $announcement->title }}</span>
            </nav>

            <article class="space-y-8 rounded-[24px] border border-[#0b3019]/15 bg-white p-10 shadow-lg shadow-[#0b3019]/5">
                <header class="space-y-4">
                    <div class="flex flex-wrap items-center gap-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">{{ $announcement->type_label ?? Str::headline($announcement->type) }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">Priority: {{ $announcement->priority_label ?? Str::headline($announcement->priority) }}</span>
                        @if ($announcement->author)
                            <span class="text-slate-400">By {{ $announcement->author->fullname ?? $announcement->author->username }}</span>
                        @endif
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-3xl font-semibold text-[#0b3019] md:text-4xl">{{ $announcement->title }}</h1>
                        <p class="text-sm text-slate-500">Published {{ $announcement->published_at?->format('M j, Y · g:i A') }}</p>
                    </div>
                </header>

                @if ($announcement->excerpt)
                    <p class="rounded-3xl bg-[#0b3019]/5 px-5 py-4 text-sm font-medium text-[#0b3019]">{{ $announcement->excerpt }}</p>
                @endif

                <div class="prose prose-slate max-w-none prose-headings:text-[#0b3019] prose-a:text-[#0b3019] prose-strong:text-[#0b3019]">
                    {!! $renderedContent ?: nl2br(e($announcement->content)) !!}
                </div>

                <footer class="flex flex-col gap-4 rounded-[20px] border border-slate-200 bg-slate-50/70 p-6 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="font-semibold text-[#0b3019]">Need to revisit later?</p>
                        <p class="text-sm text-slate-500">This announcement will stay in your inbox – you can always access it from the announcements hub.</p>
                    </div>
                    <a href="{{ route('student.announcements.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#0b3019]/30 px-5 py-2 text-sm font-semibold text-[#0b3019] transition hover:-translate-y-0.5 hover:border-[#0b3019]/50">
                        <i class="ri-arrow-left-line"></i>
                        Back to announcements
                    </a>
                </footer>
            </article>

            @if ($related->isNotEmpty())
                <section class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#0b3019]">More announcements</h2>
                    <div class="space-y-4">
                        @foreach ($related as $item)
                            <article class="group rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-[#0b3019]/10">
                                <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">{{ Str::headline($item->type) }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">{{ Str::headline($item->priority) }}</span>
                                </div>
                                <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $item->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $item->excerpt ?? Str::limit(strip_tags($item->content), 140) }}</p>
                                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-medium text-slate-500">
                                    <span class="flex items-center gap-1.5"><i class="ri-time-line"></i> {{ $item->published_at?->format('M j, Y · g:i A') }}</span>
                                    <a href="{{ route('student.announcements.show', $item) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#0b3019] transition hover:text-[#0b3019]/80">
                                        Read
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
