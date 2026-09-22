@foreach ($resources as $resource)
<tr>
    <td class="px-6 py-4">
        <div class="space-y-1">
            <p class="font-semibold text-slate-900">{{ $resource->title }}</p>
            <p class="text-xs text-slate-500">{{ Str::limit($resource->description, 90) }}</p>
            <div class="flex flex-wrap gap-2 pt-2">
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-600">
                    {{ Str::headline($resource->content_type) }}
                </span>
            </div>
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            <i class="ri-{{ $resource->resource_type === 'file' ? 'file-line' : 'external-link-line' }} text-sm"></i>
            {{ ucfirst($resource->resource_type) }}
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $classes = $resource->target_classes ?? [];
                $years = $resource->target_years ?? [];
            @endphp
            @if (empty($classes) && empty($years))
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <i class="ri-user-shared-line text-sm"></i>
                    All students
                </span>
            @endif
            @foreach ($classes as $class)
                <span class="inline-flex items-center gap-1 rounded-full bg-[#0b3019]/10 px-3 py-1 text-xs font-semibold text-[#0b3019]">
                    <i class="ri-stack-line text-sm"></i>
                    {{ $class }}
                </span>
            @endforeach
            @foreach ($years as $year)
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                    <i class="ri-graduation-cap-line text-sm"></i>
                    Year {{ $year }}
                </span>
            @endforeach
        </div>
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex justify-end gap-2">
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
    </td>
</tr>
@endforeach
