@php $title = $title ?? 'Review registration'; @endphp

<x-layouts.admin :title="$title">
    @php
        $student = $registration->student;
        $statusStyles = [
            'approved' => ['pill' => 'bg-emerald-50 text-emerald-700', 'icon' => 'ri-checkbox-circle-line'],
            'rejected' => ['pill' => 'bg-rose-50 text-rose-600', 'icon' => 'ri-close-circle-line'],
            'submitted' => ['pill' => 'bg-sky-50 text-sky-700', 'icon' => 'ri-time-line'],
            'in_progress' => ['pill' => 'bg-amber-50 text-amber-700', 'icon' => 'ri-draft-line'],
            'default' => ['pill' => 'bg-slate-100 text-slate-600', 'icon' => 'ri-information-line'],
        ];
        $style = $statusStyles[$registration->status] ?? $statusStyles['default'];
    @endphp

    <div class="mx-auto w-full max-w-5xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.course-registrations.index') }}" class="hover:text-[#0b3019] transition-colors">Course registrations</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Review</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $student?->fullname ?? $student?->username ?? 'Unknown student' }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $style['pill'] }}">
                        <i class="{{ $style['icon'] }} text-sm"></i>
                        {{ Str::headline($registration->status) }}
                    </span>
                    <span class="text-xs text-slate-400">{{ $student?->email }} · {{ $student?->class }} · Year {{ $student?->year }}</span>
                </div>
            </div>
            <a href="{{ route('admin.course-registrations.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back to registrations
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
                <div class="flex items-center gap-2">
                    <i class="ri-check-double-line text-base text-emerald-600"></i>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <div class="grid gap-5 lg:grid-cols-3">
            <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:col-span-2 animate-fade-slide animate-fade-slide-delay-200">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Uploaded documents</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Review the PDF submitted by the student before approving or rejecting the request.</p>
                </div>

                <div class="space-y-4 text-sm text-slate-600">
                    @if ($documents->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($documents as $doc)
                                <li class="flex flex-col gap-3 rounded-xl border border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0b3019]/10 text-[#0b3019]">
                                            <i class="ri-file-pdf-line text-lg"></i>
                                        </span>
                                        <div class="space-y-1">
                                            <a href="{{ $doc['url'] }}" target="_blank" rel="noopener" class="font-semibold text-[#0b3019] hover:underline">{{ $doc['name'] }}</a>
                                            <p class="text-xs text-slate-500">Uploaded {{ optional($registration->submitted_at)->diffForHumans() ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ $doc['url'] }}" download class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                            <i class="ri-download-2-line text-sm"></i>
                                            Download
                                        </a>
                                        <a href="{{ $doc['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
                                            <i class="ri-eye-line text-sm"></i>
                                            Preview
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-5 py-10 text-center text-sm text-slate-500">
                            <i class="ri-file-forbid-line text-3xl text-slate-300"></i>
                            <p class="mt-3 font-semibold text-slate-600">No PDF uploaded yet.</p>
                            <p>The student has not provided a registration document.</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Review decision</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update the status and leave a note. Students can see your comment instantly.</p>
                </div>

                <form method="POST" action="{{ url('/admin/course-registrations/'.$registration->getKey()) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-1.5">
                        <label for="status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                        <div class="relative">
                            <select id="status" name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @selected(old('status', $registration->status) === $statusOption)>{{ Str::headline($statusOption) }}</option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('status')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="admin_comment" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Comment</label>
                        <textarea id="admin_comment" name="admin_comment" rows="5" placeholder="Share feedback or next steps" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-xs text-slate-900 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">{{ old('admin_comment', $registration->admin_comment) }}</textarea>
                        @error('admin_comment')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-400">Visible to the student — keep it clear and actionable.</p>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50/40 px-4 py-3 text-xs text-slate-500">
                        <p class="font-semibold text-slate-600 mb-2">Submission details</p>
                        <ul class="space-y-1.5">
                            <li class="flex items-center justify-between"><span>Submitted</span><span class="tabular-nums">{{ $registration->submitted_at ? $registration->submitted_at->format('M j, Y · g:i A') : '—' }}</span></li>
                            <li class="flex items-center justify-between"><span>Approved</span><span class="tabular-nums">{{ $registration->approved_at ? $registration->approved_at->format('M j, Y · g:i A') : 'Pending' }}</span></li>
                            <li class="flex items-center justify-between"><span>Pending documents</span><span>{{ $registration->pending_documents ?? 0 }}</span></li>
                        </ul>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <a href="{{ route('admin.course-registrations.index') }}" class="h-9 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                            Cancel
                        </a>
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                            <i class="ri-save-3-line text-sm"></i>
                            Save decision
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
