@props(['entry'])

<div class="space-y-4">
    <div class="grid gap-4 md:grid-cols-2">
        <div class="flex flex-col gap-1.5">
            <label for="title" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Milestone title <span class="text-rose-500">*</span></label>
            <input id="title" name="title" type="text" value="{{ old('title', $entry->title) }}" required maxlength="150" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="e.g. Semester registration opens">
            @error('title')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-col gap-1.5">
            <label for="academic_year" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic year</label>
            <input id="academic_year" name="academic_year" type="text" value="{{ old('academic_year', $entry->academic_year) }}" maxlength="15" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="e.g. 2024/2025">
            @error('academic_year')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="flex flex-col gap-1.5">
        @php($startValue = old('starts_at', optional($entry->starts_at)->format('Y-m-d')))
        <label for="starts_at" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Date <span class="text-rose-500">*</span></label>
        <input id="starts_at" name="starts_at" type="date" value="{{ $startValue }}" required class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
        @error('starts_at')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/40 px-4 py-3">
        <div>
            <p class="text-xs font-semibold text-slate-700">Publish immediately</p>
            <p class="text-xs text-slate-500 mt-0.5">Students will see this milestone on the timeline when enabled.</p>
        </div>
        <label class="relative inline-flex cursor-pointer items-center">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" class="peer sr-only" @checked(old('is_published', $entry->is_published ?? true))>
            <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-[#0b3019]"></span>
            <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
        </label>
    </div>
</div>
