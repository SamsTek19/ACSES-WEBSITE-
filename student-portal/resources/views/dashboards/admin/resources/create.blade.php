@php($title = $title ?? 'Add academic resource')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.resources.index') }}" class="hover:text-[#0b3019] transition-colors">Resources</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>New resource</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create a new resource</h1>
                <p class="text-sm text-slate-500">Upload files or share links and target them to the right class and year cohorts.</p>
            </div>
            <a href="{{ route('admin.resources.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm" aria-hidden="true"></i>
                Back to list
            </a>
        </header>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <div class="flex items-start gap-3">
                    <i class="ri-error-warning-line text-base text-rose-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-xs">Please resolve the highlighted fields.</p>
                        <ul class="mt-1.5 list-disc pl-4 text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data" class="space-y-5" x-data="resourceForm({
                resourceType: '{{ old('resource_type', $resource->resource_type) }}'
            })">
            @csrf

            <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900">Resource details</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="title" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Title</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="ri-edit-line text-sm" aria-hidden="true"></i>
                            </span>
                            <input id="title" name="title" type="text" value="{{ old('title', $resource->title) }}" class="h-9 w-full rounded-lg border border-slate-200 pl-9 pr-3 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" required>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="content_type" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Content type</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="ri-stack-line text-sm" aria-hidden="true"></i>
                            </span>
                            <select id="content_type" name="content_type" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-9 pr-8 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" required>
                                @foreach ($contentTypes as $type)
                                    <option value="{{ $type }}" @selected(old('content_type', $resource->content_type) === $type)>{{ Str::headline($type) }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex flex-col gap-1.5">
                        <label for="description" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">{{ old('description', $resource->description) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200" x-cloak>
                <h2 class="text-sm font-bold text-slate-900">Delivery type</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Resource type</span>
                        @php($resourceTypeIcons = [
                            'link' => 'ri-external-link-line',
                            'file' => 'ri-file-3-line',
                            'video' => 'ri-play-circle-line',
                            'handout' => 'ri-book-open-line',
                            'past_question' => 'ri-question-line',
                            'default' => 'ri-stack-line',
                        ])
                        <input type="hidden" name="resource_type" x-model="resourceType">
                        <div class="grid gap-2 sm:grid-cols-3">
                            @foreach ($resourceTypes as $type)
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition" :class="resourceType === '{{ $type }}' ? 'border-[#0b3019] bg-[#0b3019]/10 text-[#0b3019]' : 'border-slate-200 text-slate-600 hover:border-[#0b3019]/40 hover:text-[#0b3019]'" @click="resourceType = '{{ $type }}'">
                                    <i class="{{ $resourceTypeIcons[$type] ?? $resourceTypeIcons['default'] }} text-sm" aria-hidden="true"></i>
                                    <span class="capitalize">{{ Str::headline($type) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-3" x-show="resourceType !== 'file'" x-cloak>
                        <div class="flex flex-col gap-1.5">
                            <label for="cta_url" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Link URL</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="ri-links-line text-sm" aria-hidden="true"></i>
                                </span>
                                <input id="cta_url" name="cta_url" type="url" value="{{ old('cta_url', $resource->cta_url) }}" placeholder="https://" class="h-9 w-full rounded-lg border border-slate-200 pl-9 pr-3 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="cta_label" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Link label</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="ri-edit-box-line text-sm" aria-hidden="true"></i>
                                </span>
                                <input id="cta_label" name="cta_label" type="text" value="{{ old('cta_label', $resource->cta_label) }}" placeholder="e.g. Open resource" class="h-9 w-full rounded-lg border border-slate-200 pl-9 pr-3 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2" x-show="resourceType === 'file'" x-cloak>
                        <label for="file" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Upload file</label>
                        <label for="file" class="mt-2 flex w-full cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-[#0b3019]/40 bg-[#0b3019]/5 px-6 py-5 text-sm text-slate-600 hover:border-[#0b3019]/60">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-[#0b3019] shadow">
                                <i class="ri-upload-cloud-2-line text-lg" aria-hidden="true"></i>
                            </span>
                            <span class="text-xs font-semibold text-[#0b3019]">Choose resource file</span>
                            <span class="text-xs text-slate-500">Maximum 50MB · pdf, doc(x), ppt(x), xls(x), zip, mp4, mov, avi</span>
                            <input id="file" name="file" type="file" class="hidden" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.mp4,.mov,.avi">
                        </label>
                    </div>
                </div>
            </section>

            <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-sm font-bold text-slate-900">Audience targeting</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target classes</span>
                        <p class="mt-0.5 text-xs text-slate-500">Leave all unchecked to show to every class.</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($classOptions as $class)
                                <label class="inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1 text-xs font-semibold transition cursor-pointer" :class="selectedClasses.includes('{{ $class }}') ? 'border-[#0b3019] bg-[#0b3019]/10 text-[#0b3019]' : 'border-slate-200 text-slate-600 hover:border-[#0b3019]/40 hover:text-[#0b3019]'">
                                    <input type="checkbox" name="target_classes[]" value="{{ $class }}" class="sr-only" @change="toggleClass('{{ $class }}')" @checked(in_array($class, old('target_classes', $resource->target_classes ?? [])))>
                                    <i class="ri-team-line text-sm" aria-hidden="true"></i>
                                    <span>{{ $class }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target years</span>
                        <p class="mt-0.5 text-xs text-slate-500">Leave all unchecked to show to all academic levels.</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($yearOptions as $year)
                                <label class="inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1 text-xs font-semibold transition cursor-pointer" :class="selectedYears.includes('{{ $year }}') ? 'border-[#0b3019] bg-[#0b3019]/10 text-[#0b3019]' : 'border-slate-200 text-slate-600 hover:border-[#0b3019]/40 hover:text-[#0b3019]'">
                                    <input type="checkbox" name="target_years[]" value="{{ $year }}" class="sr-only" @change="toggleYear('{{ $year }}')" @checked(in_array($year, old('target_years', $resource->target_years ?? [])))>
                                    <i class="ri-graduation-cap-line text-sm" aria-hidden="true"></i>
                                    <span>Year {{ $year }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('admin.resources.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                    <i class="ri-save-line text-sm" aria-hidden="true"></i>
                    Save resource
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('resourceForm', (state) => ({
                resourceType: state.resourceType || 'link',
                selectedClasses: @js(old('target_classes', $resource->target_classes ?? [])),
                selectedYears: @js(old('target_years', $resource->target_years ?? [])),
                toggleClass(value) {
                    this.selectedClasses = this.toggleArray(this.selectedClasses, value);
                },
                toggleYear(value) {
                    this.selectedYears = this.toggleArray(this.selectedYears, value);
                },
                toggleArray(list, value) {
                    const index = list.indexOf(value);
                    if (index === -1) {
                        list.push(value);
                    } else {
                        list.splice(index, 1);
                    }
                    return list;
                },
            }));
        });
    </script>
</x-layouts.admin>
