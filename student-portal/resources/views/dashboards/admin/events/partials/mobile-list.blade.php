<ul class="divide-y divide-slate-100 bg-white">
    @forelse ($events as $event)
        <li class="space-y-4 px-4 py-5">
            <div class="relative mb-3 aspect-[16/9] w-full overflow-hidden rounded-2xl bg-slate-100">
                @if ($event->banner_url)
                    <img
                        src="{{ $event->banner_url }}"
                        alt="{{ $event->banner_alt ?? ($event->title . ' banner') }}"
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

            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">{{ $event->title }}</h3>
                <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/40 hover:text-[#0b3019]">
                    <i class="ri-edit-line text-sm" aria-hidden="true"></i>
                    Edit
                </a>
            </div>
            @if ($event->description)
                <p class="text-sm text-slate-600">{{ Str::limit(strip_tags($event->description), 160) }}</p>
            @endif

            <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]">
                    <i class="ri-time-fill text-sm" aria-hidden="true"></i>
                    {{ optional($event->start_at)->format('M j, Y · g:i A') ?? 'TBA' }}
                </span>
                @if ($event->end_at)
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
                        <i class="ri-timer-2-line text-sm" aria-hidden="true"></i>
                        Ends {{ $event->end_at->format('M j, Y · g:i A') }}
                    </span>
                @endif
                @if ($event->location)
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
                        <i class="ri-map-pin-2-fill text-sm" aria-hidden="true"></i>
                        {{ $event->location }}
                    </span>
                @endif
                @if ($event->category)
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]">
                        <i class="ri-price-tag-3-line text-sm" aria-hidden="true"></i>
                        {{ Str::headline($event->category) }}
                    </span>
                @endif
                @if ($event->cta_url)
                    <a href="{{ $event->cta_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#0b3019] shadow-sm hover:underline">
                        <i class="ri-external-link-line text-sm" aria-hidden="true"></i>
                        Call-to-action
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1">
                    <i class="ri-calendar-event-line" aria-hidden="true"></i>
                    Created {{ optional($event->created_at)->diffForHumans() }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This action cannot be undone.');" class="pt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-9 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 text-xs font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-100 active:scale-95">
                    <i class="ri-delete-bin-6-line text-sm" aria-hidden="true"></i>
                    Delete event
                </button>
            </form>
        </li>
    @empty
        <li class="px-4 py-16 text-center text-sm text-slate-500">
            <div class="mx-auto flex max-w-md flex-col items-center gap-4">
                <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#0b3019]/10 text-[#0b3019]">
                    <i class="ri-calendar-line text-3xl" aria-hidden="true"></i>
                </span>
                <p class="text-base font-semibold text-slate-700">No events scheduled yet</p>
                <p class="text-sm text-slate-500">Get started by creating your first event. It will appear here once saved.</p>
                <a href="{{ route('admin.events.create') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                    <i class="ri-add-line text-base" aria-hidden="true"></i>
                    Create event
                </a>
            </div>
        </li>
    @endforelse
</ul>
