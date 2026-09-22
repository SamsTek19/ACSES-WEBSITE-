@forelse ($events as $event)
    <tr class="transition hover:bg-[#0b3019]/5">
        <td class="px-6 py-4 align-top">
            <div class="flex items-start gap-4">
                <div class="relative aspect-[16/9] w-40 overflow-hidden rounded-2xl bg-slate-100">
                    @if ($event->banner_url)
                        <img
                            src="{{ $event->banner_url }}"
                            alt="{{ $event->banner_alt ?? ($event->title . ' banner') }}"
                            loading="lazy"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#0b3019]/5 via-slate-100 to-[#0b3019]/5">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#0b3019]/10 text-[#0b3019]">
                                <i class="ri-image-line text-xl" aria-hidden="true"></i>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <p class="font-semibold text-slate-900">{{ $event->title }}</p>
                    @if ($event->description)
                        <p class="text-xs text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($event->description), 120) }}</p>
                    @endif
                    <p class="text-xs text-slate-400">Created {{ optional($event->created_at)->diffForHumans() }}</p>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 align-top text-sm text-slate-700">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i class="ri-time-fill text-base text-[#0b3019]" aria-hidden="true"></i>
                    <span>{{ optional($event->start_at)->format('M j, Y · g:i A') ?? 'TBA' }}</span>
                </div>
                @if ($event->end_at)
                    <p class="flex items-center gap-2 text-xs text-slate-500">
                        <i class="ri-timer-2-line text-sm" aria-hidden="true"></i>
                        Ends {{ $event->end_at->format('M j, Y · g:i A') }}
                    </p>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 align-top">
            @if ($event->location)
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                    <i class="ri-map-pin-2-fill text-sm" aria-hidden="true"></i>
                    {{ $event->location }}
                </span>
            @else
                <span class="text-xs text-slate-400">TBA</span>
            @endif
        </td>
        <td class="px-6 py-4 align-top">
            <div class="flex flex-col gap-2">
                @if ($event->category)
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]">
                        <i class="ri-price-tag-3-line text-sm" aria-hidden="true"></i>
                        {{ Str::headline($event->category) }}
                    </span>
                @else
                    <span class="text-xs text-slate-400">General</span>
                @endif

                @if ($event->cta_url)
                    <a href="{{ $event->cta_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0b3019] hover:underline">
                        <i class="ri-external-link-line text-sm" aria-hidden="true"></i>
                        Call-to-action
                    </a>
                @endif
            </div>
        </td>
        <td class="px-6 py-4 align-top">
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/40 hover:text-[#0b3019]" aria-label="Edit event">
                    <i class="ri-edit-line text-sm" aria-hidden="true"></i>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-100" aria-label="Delete event">
                        <i class="ri-delete-bin-6-line text-sm" aria-hidden="true"></i>
                        Delete
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">
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
        </td>
    </tr>
@endforelse
