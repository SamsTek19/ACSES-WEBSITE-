@foreach ($resources as $resource)
<article class="flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
    <header class="space-y-2">
        <h3 class="text-lg font-semibold text-slate-900">{{ $resource->title }}</h3>
        <p class="text-sm text-slate-600">{{ Str::limit($resource->description, 120) }}</p>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                <i class="ri-{{ $resource->resource_type === 'file' ? 'file-line' : 'external-link-line' }} text-sm"></i>
                {{ ucfirst($resource->resource_type) }}
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold text-[#0b3019]">
                {{ Str::headline($resource->content_type) }}
            </span>
        </div>
    </header>
    <div class="flex flex-wrap gap-2 text-xs text-slate-600">
        @php
            $classes = $resource->target_classes ?? [];
            $years = $resource->target_years ?? [];
        @endphp
        @if (empty($classes) && empty($years))
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">
                <i class="ri-user-shared-line text-sm"></i>
                All students
            </span>
        @endif
        @foreach ($classes as $class)
            <span class="inline-flex items-center gap-1 rounded-full bg-[#0b3019]/10 px-3 py-1 font-semibold text-[#0b3019]">
                <i class="ri-stack-line text-sm"></i>
                {{ $class }}
            </span>
        @endforeach
        @foreach ($years as $year)
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-3 py-1 font-semibold text-slate-700">
                <i class="ri-graduation-cap-line text-sm"></i>
                Year {{ $year }}
            </span>
        @endforeach
    </div>
    <footer class="flex items-center justify-between border-t border-slate-200 pt-4">
        <span class="inline-flex items-center gap-2 rounded-full {{ $resource->visibility === 'student' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-semibold">
            <i class="ri-eye-{{ $resource->visibility === 'student' ? '2-line' : 'close-line' }} text-sm"></i>
            {{ $resource->visibility === 'student' ? 'Visible' : 'Hidden' }}
        </span>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.resources.edit', $resource) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/40 hover:text-[#0b3019]">
                <i class="ri-edit-line text-sm"></i>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" class="inline-flex" onsubmit="return confirm('Delete this resource?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                    <i class="ri-delete-bin-line text-sm"></i>
                    Delete
                </button>
            </form>
        </div>
    </footer>
</article>
@endforeach
