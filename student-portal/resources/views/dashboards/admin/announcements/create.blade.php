@php
    use Illuminate\Support\Str;
    $title = 'Send announcement';
    $targetType = old('target_type', 'all');
@endphp

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-5xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.announcements.index') }}" class="hover:text-[#0b3019] transition-colors">Announcements</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>New announcement</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Share updates with students</h1>
                <p class="text-sm text-slate-500">Target all students or focus on a specific audience. Announcements are sent instantly via email.</p>
            </div>
            <a href="{{ route('admin.announcements.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back
            </a>
        </header>

        @include('dashboards.admin.announcements.partials.form', [
            'types' => $types,
            'priorities' => $priorities,
            'targetTypes' => $targetTypes,
            'options' => $options,
        ])
    </div>
</x-layouts.admin>


