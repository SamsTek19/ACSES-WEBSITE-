@php
    /** @var \App\Models\Announcement|null $announcement */
    $announcement = $announcement ?? null;
    $targetFilters = $targetFilters ?? [];
    $isEdit = $announcement?->exists ?? false;
    $action = $isEdit ? route('admin.announcements.update', $announcement) : route('admin.announcements.store');
    $targetType = old('target_type', $announcement->target_type ?? 'all');
    $selectedClasses = collect(old('classes', $targetFilters['classes'] ?? []))->map(fn ($value) => (string) $value)->all();
    $selectedYears = collect(old('years', $targetFilters['years'] ?? []))->map(fn ($value) => (int) $value)->all();
    $selectedStudents = collect(old('student_ids', $targetFilters['students'] ?? []))->map(fn ($value) => (int) $value)->all();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8" x-data="{ targetType: @js($targetType) }">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="space-y-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900">Announcement details</h2>
        <div class="grid gap-5 md:grid-cols-2">
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Title</span>
                <input type="text" name="title" value="{{ old('title', $announcement->title ?? '') }}" required maxlength="160" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="Exam timetable update" />
                @error('title')
                    <span class="text-xs text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Excerpt <span class="text-slate-300">(optional)</span></span>
                <input type="text" name="excerpt" value="{{ old('excerpt', $announcement->excerpt ?? '') }}" maxlength="255" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="Brief summary shown in lists" />
                @error('excerpt')
                    <span class="text-xs text-rose-600">{{ $message }}</span>
                @enderror
            </label>
        </div>
        <label class="flex flex-col gap-2">
            <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Message body</span>
            <textarea name="content" rows="8" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs leading-6 text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" placeholder="Include all necessary context, links, and next steps for students.">{{ old('content', $announcement->content ?? '') }}</textarea>
            @error('content')
                <span class="text-xs text-rose-600">{{ $message }}</span>
            @enderror
        </label>
        <div class="grid gap-5 md:grid-cols-3">
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Type</span>
                <div class="relative">
                    <select name="type" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $announcement->type ?? 'general') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Priority</span>
                <div class="relative">
                    <select name="priority" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @foreach ($priorities as $value => $label)
                            <option value="{{ $value }}" @selected(old('priority', $announcement->priority ?? 'normal') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Audience</span>
                <div class="relative">
                    <select name="target_type" x-model="targetType" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @foreach ($targetTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                @error('target_type')
                    <span class="text-xs text-rose-600">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="space-y-4" x-cloak>
            <div x-show="['class', 'class_year'].includes(targetType)" x-transition>
                <label class="flex flex-col gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Select class</span>
                    <select name="classes[]" multiple class="min-h-[120px] rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @foreach ($options['classes'] as $class)
                            <option value="{{ $class }}" @selected(in_array($class, $selectedClasses, true))>{{ $class }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-slate-400">Hold Ctrl / Cmd to select multiple classes. Applies to class-only, class &amp; year, or year audiences.</span>
                    @error('classes')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div x-show="['year', 'class_year'].includes(targetType)" x-transition>
                <label class="flex flex-col gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Select year</span>
                    <select name="years[]" multiple class="min-h-[120px] rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        @foreach ($options['years'] as $year)
                            <option value="{{ $year }}" @selected(in_array((int) $year, $selectedYears, true))>Year {{ $year }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-slate-400">Hold Ctrl / Cmd to select multiple years. Required for year-only, class &amp; year, or class audiences.</span>
                    @error('years')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div x-show="targetType === 'student'" x-transition x-data="studentSelector({
                    students: @js(collect($options['students'])->map(fn($label, $id) => ['id' => $id, 'label' => $label])->values()->all()),
                    selectedIds: @js($selectedStudents)
                })">
                <label class="flex flex-col gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Select students</span>
                    
                    {{-- Search Input --}}
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="ri-search-line text-base" aria-hidden="true"></i>
                        </span>
                        <input 
                            type="text" 
                            x-model="search" 
                            placeholder="Search by name, username, email, or reference..." 
                            class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-4 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]"
                        >
                        <button 
                            type="button" 
                            x-show="search.length > 0" 
                            @click="search = ''" 
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600"
                        >
                            <i class="ri-close-line text-base"></i>
                        </button>
                    </div>
                    
                    {{-- Selected Students Tags --}}
                    <div x-show="selectedIds.length > 0" class="flex flex-wrap gap-2 p-2 rounded-lg border border-slate-200 bg-slate-50">
                        <template x-for="id in selectedIds" :key="id">
                            <span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019] px-2 py-0.5 text-xs font-semibold text-white">
                                <span x-text="getStudentLabel(id)"></span>
                                <button type="button" @click="toggleStudent(id)" class="hover:text-emerald-200">
                                    <i class="ri-close-line text-sm"></i>
                                </button>
                            </span>
                        </template>
                    </div>
                    
                    {{-- Student List - Only shows when searching --}}
                    <div class="rounded-lg border border-slate-200 bg-white">
                        {{-- Prompt to search --}}
                        <template x-if="search.length < 2">
                            <div class="px-4 py-8 text-center text-sm text-slate-400">
                                <i class="ri-search-line text-3xl mb-3 block text-slate-300"></i>
                                <p class="font-medium text-slate-500">Search for students</p>
                                <p class="mt-1">Type at least 2 characters to find students by name, email, or reference number</p>
                            </div>
                        </template>
                        
                        {{-- Search results --}}
                        <template x-if="search.length >= 2">
                            <div class="max-h-[250px] overflow-y-auto">
                                <template x-if="filteredStudents.length === 0">
                                    <div class="px-4 py-6 text-center text-sm text-slate-400">
                                        <i class="ri-user-search-line text-2xl mb-2"></i>
                                        <p>No students match "<span x-text="search"></span>"</p>
                                    </div>
                                </template>
                                <template x-for="student in filteredStudents" :key="student.id">
                                    <label 
                                        class="flex cursor-pointer items-center gap-3 px-4 py-3 transition hover:bg-slate-50 border-b border-slate-100 last:border-b-0"
                                        :class="{ 'bg-[#0b3019]/5': isSelected(student.id) }"
                                    >
                                        <input 
                                            type="checkbox" 
                                            :checked="isSelected(student.id)"
                                            @change="toggleStudent(student.id)"
                                            class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]"
                                        >
                                        <span class="text-sm text-slate-700" x-text="student.label"></span>
                                    </label>
                                </template>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Hidden inputs for form submission --}}
                    <template x-for="id in selectedIds" :key="'input-' + id">
                        <input type="hidden" name="student_ids[]" :value="id">
                    </template>
                    
                    <span class="text-xs text-slate-400">
                        <span x-text="selectedIds.length"></span> student(s) selected
                    </span>
                    @error('student_ids')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </div>
    </section>

    <div class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-slate-500">Announcements are delivered immediately via email and appear in the student announcement hub.</p>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.announcements.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">Cancel</a>
            <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                <i class="ri-send-plane-line text-sm"></i>
                {{ $isEdit ? 'Update announcement' : 'Send announcement' }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('studentSelector', ({ students = [], selectedIds = [] }) => ({
            students: students,
            selectedIds: selectedIds.map(id => parseInt(id)),
            search: '',
            
            get filteredStudents() {
                // Only show results when search has at least 2 characters
                if (this.search.trim().length < 2) {
                    return [];
                }
                const searchLower = this.search.toLowerCase();
                return this.students.filter(student => 
                    student.label.toLowerCase().includes(searchLower)
                );
            },
            
            isSelected(id) {
                return this.selectedIds.includes(parseInt(id));
            },
            
            toggleStudent(id) {
                id = parseInt(id);
                if (this.isSelected(id)) {
                    this.selectedIds = this.selectedIds.filter(sid => sid !== id);
                } else {
                    this.selectedIds.push(id);
                }
            },
            
            getStudentLabel(id) {
                const student = this.students.find(s => parseInt(s.id) === parseInt(id));
                return student ? student.label : 'Unknown';
            }
        }));
    });
</script>
@endpush
