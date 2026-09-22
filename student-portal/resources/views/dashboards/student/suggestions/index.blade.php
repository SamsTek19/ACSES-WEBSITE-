<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    <div class="mx-auto w-full max-w-[1600px] space-y-10 px-4 py-12 sm:px-6 lg:px-8">
        <div class="space-y-10">
            <section class="hidden sm:block animate-fade-slide overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)]">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-100">Feedback</span>
                        <div class="space-y-2">
                            <h1 class="text-3xl font-semibold md:text-4xl">Suggestion box</h1>
                            <p class="max-w-2xl text-sm text-emerald-100/85">
                                Share ideas, highlight concerns, or request improvements. The ACSES support team reviews every submission and will follow up when required.
                            </p>
                        </div>
                    </div>
                    <div class="rounded-[24px] border border-white/20 bg-white/10 px-6 py-4 text-sm text-emerald-50 shadow-inner">
                        <p class="font-semibold uppercase tracking-[0.25em] text-emerald-200">Response window</p>
                        <p class="mt-2 leading-6">Team replies typically arrive within <span class="font-semibold">2 business days</span>. Remember to include as many details as possible for faster resolution.</p>
                    </div>
                </div>
            </section>

            @if (session('status'))
                <div class="animate-fade-slide rounded-[24px] border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm">
                    <div class="flex items-start gap-3">
                        <i class="ri-checkbox-circle-line text-xl" aria-hidden="true"></i>
                        <div>
                            <p class="text-sm font-semibold">Submission received</p>
                            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="animate-fade-slide rounded-[24px] border border-rose-200 bg-rose-50 p-4 text-rose-900 shadow-sm">
                    <div class="flex items-start gap-3">
                        <i class="ri-error-warning-line text-xl" aria-hidden="true"></i>
                        <div>
                            <p class="text-sm font-semibold">Please fix the highlighted fields</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm text-rose-800">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">
                <section class="lg:col-span-2">
                    <article class="animate-fade-slide rounded-[24px] border border-slate-200/80 bg-white p-6 shadow-sm">
                        <header>
                            <h2 class="text-lg font-semibold text-[#0b3019]">Submit a suggestion</h2>
                            <p class="text-sm text-slate-500">All fields marked with * are required. Attach relevant screenshots or files if available (max 4&nbsp;MB).</p>
                        </header>

                        <form method="POST" action="{{ route('student.suggestions.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf

                            <div class="grid gap-6 sm:grid-cols-2">
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Category *</span>
                                    <div class="relative">
                                        <select name="category" class="w-full appearance-none rounded-[16px] border border-slate-200 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                            @foreach ($categories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>
                                    </div>
                                </label>

                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Subject *</span>
                                    <input type="text" name="subject" value="{{ old('subject') }}" maxlength="160" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40" placeholder="Give a short headline">
                                </label>
                            </div>

                            <label class="flex flex-col gap-2">
                                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Message *</span>
                                <textarea name="message" rows="6" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40" placeholder="Explain the idea, improvement, or issue in detail. Include specific examples or references.">{{ old('message') }}</textarea>
                            </label>

                            <label class="flex flex-col gap-3 rounded-[16px] border border-dashed border-slate-300 bg-slate-50/60 px-4 py-6 text-sm text-slate-600">
                                <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Attachment (optional)</span>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-slate-700">Add supporting files</p>
                                        <p class="text-xs text-slate-500">Accepted formats: PNG, JPG, PDF, DOCX (max 4&nbsp;MB)</p>
                                    </div>
                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#0b3019]/40 hover:text-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
                                        <i class="ri-attachment-2 text-base" aria-hidden="true"></i>
                                        <span>Upload file</span>
                                        <input type="file" name="attachment" class="sr-only" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx">
                                    </label>
                                </div>
                            </label>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#0b3019] px-6 py-3 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#094018] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                    <i class="ri-check-line text-base" aria-hidden="true"></i>
                                    Submit suggestion
                                </button>
                            </div>
                        </form>
                    </article>
                </section>

                <aside class="space-y-6">
                    <article class="animate-fade-slide rounded-[24px] border border-slate-200/80 bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">What to include</h3>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600">
                            <li class="flex gap-3">
                                <span class="mt-1 inline-flex h-5 w-5 flex-none items-center justify-center rounded-full bg-[#0b3019]/10 text-xs font-semibold text-[#0b3019]">1</span>
                                <div>
                                    <p class="font-medium text-slate-800">Clear context</p>
                                    <p class="text-xs text-slate-500">Where did you spot the issue or what area will this suggestion improve?</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 inline-flex h-5 w-5 flex-none items-center justify-center rounded-full bg-[#0b3019]/10 text-xs font-semibold text-[#0b3019]">2</span>
                                <div>
                                    <p class="font-medium text-slate-800">Desired outcome</p>
                                    <p class="text-xs text-slate-500">Explain the benefit to students, staff, or campus operations.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 inline-flex h-5 w-5 flex-none items-center justify-center rounded-full bg-[#0b3019]/10 text-xs font-semibold text-[#0b3019]">3</span>
                                <div>
                                    <p class="font-medium text-slate-800">Supporting detail</p>
                                    <p class="text-xs text-slate-500">Attach files, screenshots, or references that provide further clarity.</p>
                                </div>
                            </li>
                        </ul>
                    </article>

                    <article class="rounded-[24px] border border-slate-200/80 bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Need immediate help?</h3>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <p class="flex items-center gap-2 text-slate-700">
                                <i class="ri-phone-line text-base text-[#0b3019]" aria-hidden="true"></i>
                                Call the student services hotline on <span class="font-semibold">055 935 9824</span> (08:00–20:00 GMT)
                            </p>
                            <p class="flex items-center gap-2 text-slate-700">
                                <i class="ri-mail-line text-base text-[#0b3019]" aria-hidden="true"></i>
                                Email <a href="mailto:acsesrepos@gmail.com" class="font-semibold text-[#0b3019] underline-offset-4 hover:underline">acsesrepos@gmail.com</a>
                            </p>
                        </div>
                    </article>
                </aside>
            </div>

            <section class="space-y-4">
                <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0b3019]">Your submissions</h2>
                        <p class="text-sm text-slate-500">Track suggestions you have sent. Status updates appear here once the team reviews them.</p>
                    </div>
                    @if ($suggestions->hasPages())
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <span>Page {{ $suggestions->currentPage() }} of {{ $suggestions->lastPage() }}</span>
                            <div class="flex items-center gap-1">
                                {{ $suggestions->onEachSide(1)->links('vendor.pagination.simple-tailwind') }}
                            </div>
                        </div>
                    @endif
                </header>

                @if ($suggestions->isEmpty())
                    <article class="rounded-[24px] border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500">
                        <p>No suggestions submitted yet. Share your first idea using the form above.</p>
                    </article>
                @else
                    <div class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-sm">
                        <div class="hidden lg:block">
                            <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                                <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left">Subject</th>
                                        <th scope="col" class="px-6 py-4 text-left">Category</th>
                                        <th scope="col" class="px-6 py-4 text-left">Submitted</th>
                                        <th scope="col" class="px-6 py-4 text-left">Status</th>
                                        <th scope="col" class="px-6 py-4 text-left">Attachment</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach ($suggestions as $suggestion)
                                        <tr class="transition hover:bg-slate-50">
                                            <td class="px-6 py-4 align-top">
                                                <p class="font-medium text-slate-800">{{ $suggestion->subject }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ Str::limit($suggestion->message, 120) }}</p>
                                            </td>
                                            <td class="px-6 py-4 align-top text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $categories[$suggestion->category] ?? Str::headline($suggestion->category) }}</td>
                                            <td class="px-6 py-4 align-top text-sm text-slate-500">{{ $suggestion->created_at?->diffForHumans() }}</td>
                                            <td class="px-6 py-4 align-top">
                                                @php
                                                    $status = Str::headline($suggestion->status);
                                                    $statusStyles = [
                                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'in review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    ];
                                                    $badgeClass = $statusStyles[strtolower($status)] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                                @endphp
                                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $status }}</span>
                                            </td>
                                            <td class="px-6 py-4 align-top text-sm">
                                                @if ($suggestion->attachment_path)
                                                    <a href="{{ Storage::disk('public')->url($suggestion->attachment_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-[#0b3019] transition hover:underline">
                                                        <i class="ri-file-download-line text-base" aria-hidden="true"></i>
                                                        Download
                                                    </a>
                                                @else
                                                    <span class="text-xs text-slate-400">None</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="divide-y divide-slate-200 text-sm text-slate-600 lg:hidden">
                            @foreach ($suggestions as $suggestion)
                                <div class="space-y-3 px-4 py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $suggestion->subject }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ Str::limit($suggestion->message, 140) }}</p>
                                        </div>
                                        <span class="text-xs text-slate-400">{{ $suggestion->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-500">{{ $categories[$suggestion->category] ?? Str::headline($suggestion->category) }}</span>
                                        @php
                                            $status = Str::headline($suggestion->status);
                                            $statusStyles = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'in review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                            $badgeClass = $statusStyles[strtolower($status)] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $status }}</span>
                                    </div>
                                    <div>
                                        @if ($suggestion->attachment_path)
                                            <a href="{{ Storage::disk('public')->url($suggestion->attachment_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0b3019] transition hover:underline">
                                                <i class="ri-file-download-line text-sm" aria-hidden="true"></i>
                                                Download attachment
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">No attachment</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="pt-4">
                        {{ $suggestions->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.dashboard>
