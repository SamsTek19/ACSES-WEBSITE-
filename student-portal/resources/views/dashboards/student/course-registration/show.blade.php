<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    @php
        $status = $registration->status ?? 'not_started';
        $statusLabels = [
            'not_started' => 'Not started',
            'in_progress' => 'In progress',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
        ];
        $badgeClasses = [
            'not_started' => 'bg-white/10 text-white border-white/30',
            'in_progress' => 'bg-amber-100 text-amber-800 border-amber-300',
            'submitted' => 'bg-sky-100 text-sky-800 border-sky-300',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        ];
        $statusCopy = [
            'not_started' => 'Download the registrar PDF, sign it, and upload it here to start the review.',
            'in_progress' => 'You have a draft in progress. Upload the signed PDF whenever it is ready.',
            'submitted' => 'Your PDF is with the faculty office. We will notify you once it is reviewed.',
            'approved' => 'Registration has been approved. Keep a copy of the confirmation PDF for your records.',
        ];
        $progress = (int) ($registration->progress_percent ?? ($registration ? 40 : 0));
        $progress = min(100, max(0, $progress));
        $documents = $registration->document_paths ?? [];
        $submittedAt = $registration?->submitted_at ? \Carbon\CarbonImmutable::parse($registration->submitted_at) : null;
        $approvedAt = $registration?->approved_at ? \Carbon\CarbonImmutable::parse($registration->approved_at) : null;
        $pendingDocuments = $registration?->pending_documents ?? count($documents);
    @endphp

    <div x-data="{ loading: true }" x-init="setTimeout(() => { loading = false }, 600)" class="mx-auto w-full max-w-6xl space-y-10 px-4 py-12 sm:px-6 lg:px-8">
        <div x-show="loading" x-transition.opacity.duration.200ms class="space-y-10" role="status" aria-live="polite">
            <section class="hidden sm:block overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)]">
                <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-4">
                        <span class="skeleton inline-block h-5 w-28 rounded-full bg-white/20"></span>
                        <div class="space-y-3">
                            <div class="skeleton h-10 w-64 rounded-2xl bg-white/35"></div>
                            <div class="skeleton h-4 w-80 rounded-2xl bg-white/25"></div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-white/20 bg-white/10 px-6 py-5 text-sm shadow-inner">
                        <div class="skeleton h-3 w-32 rounded-full bg-white/30"></div>
                        <div class="mt-3 flex items-center gap-4">
                            <div class="skeleton h-7 w-32 rounded-full bg-white/30"></div>
                            <div class="skeleton h-8 w-16 rounded-full bg-white/30"></div>
                        </div>
                        <div class="mt-4 space-y-2 text-xs">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="skeleton h-3 w-48 rounded-full bg-white/20"></div>
                            @endfor
                        </div>
                    </div>
                </div>
            </section>

            <div class="space-y-6">
                <div class="skeleton h-14 w-full rounded-3xl bg-emerald-100/40"></div>
                <div class="skeleton h-14 w-full rounded-3xl bg-rose-100/40"></div>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <section class="space-y-6 lg:col-span-2">
                    <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                        <div class="space-y-4 border-b border-slate-200 pb-4">
                            <div class="skeleton h-5 w-52 rounded-full bg-slate-200"></div>
                            <div class="skeleton h-4 w-72 rounded-full bg-slate-100"></div>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="skeleton h-6 w-28 rounded-full bg-emerald-100"></div>
                                <div class="skeleton h-4 w-36 rounded-full bg-slate-100"></div>
                            </div>
                            <div class="space-y-3">
                                @for ($i = 0; $i < 2; $i++)
                                    <div class="rounded-2xl border border-slate-200 px-4 py-3">
                                        <div class="skeleton h-4 w-48 rounded-full bg-slate-100"></div>
                                    </div>
                                @endfor
                            </div>
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-6"></div>
                        </div>
                    </article>

                    <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                        <div class="space-y-4 border-b border-slate-200 pb-4">
                            <div class="skeleton h-5 w-60 rounded-full bg-slate-200"></div>
                            <div class="skeleton h-4 w-72 rounded-full bg-slate-100"></div>
                        </div>
                        <div class="mt-6 space-y-6">
                            <div class="space-y-2">
                                <div class="skeleton h-4 w-40 rounded-full bg-slate-200"></div>
                                <div class="skeleton h-12 w-full rounded-2xl bg-slate-100"></div>
                                <div class="skeleton h-3 w-48 rounded-full bg-slate-100"></div>
                            </div>
                            <div class="space-y-2 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 py-4">
                                @for ($i = 0; $i < 2; $i++)
                                    <div class="skeleton h-4 w-full rounded-full bg-slate-100"></div>
                                @endfor
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <div class="skeleton h-10 w-24 rounded-full bg-slate-100"></div>
                                <div class="skeleton h-10 w-32 rounded-full bg-[#0b3019]/20"></div>
                            </div>
                        </div>
                    </article>
                </section>

                <aside class="space-y-6">
                    <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                        <div class="skeleton h-4 w-32 rounded-full bg-slate-200"></div>
                        <div class="mt-4 space-y-4">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="flex items-start gap-3">
                                    <div class="skeleton h-6 w-6 rounded-full bg-slate-100"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="skeleton h-4 w-40 rounded-full bg-slate-200"></div>
                                        <div class="skeleton h-3 w-44 rounded-full bg-slate-100"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </article>

                    <article class="rounded-3xl border border-[#0b3019]/10 bg-[#0b3019]/5 p-6 text-slate-700 shadow-lg shadow-[#0b3019]/10">
                        <div class="skeleton h-4 w-32 rounded-full bg-[#0b3019]/20"></div>
                        <div class="mt-4 space-y-3">
                            <div class="skeleton h-4 w-48 rounded-full bg-[#0b3019]/15"></div>
                            <div class="space-y-3">
                                @for ($i = 0; $i < 2; $i++)
                                    <div class="flex items-center gap-3">
                                        <div class="skeleton h-8 w-8 rounded-full bg-white/70"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="skeleton h-4 w-36 rounded-full bg-[#0b3019]/20"></div>
                                            <div class="skeleton h-3 w-40 rounded-full bg-[#0b3019]/15"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </div>

        <div x-show="!loading" x-transition.opacity.duration.200ms x-cloak class="space-y-10">
            @if (session('status'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-700 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        <p>{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm text-rose-700 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                        <div>
                            <p class="font-semibold">Please resolve the following:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <section class="hidden sm:block animate-fade-slide overflow-hidden rounded-[24px] border border-[#0b3019]/15 bg-gradient-to-br from-[#0b3019] via-[#114127] to-[#0b3019] p-8 text-white shadow-[0_20px_50px_-30px_rgba(11,48,25,0.4)]">
            <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-100">Registration</span>
                    <div class="space-y-2">
                        <h1 class="text-3xl font-semibold md:text-4xl">Course registration</h1>
                        <p class="max-w-2xl text-sm text-emerald-100/85">
                            Submit your semester course selections, upload documents, and track approval progress from one place.
                        </p>
                    </div>
                </div>
                <div class="rounded-3xl border border-white/15 bg-white/10 px-6 py-5 text-sm shadow-inner">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-200">Current status</p>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClasses[$status] ?? $badgeClasses['not_started'] }}">
                            {{ $statusLabels[$status] ?? \Illuminate\Support\Str::headline($status) }}
                        </span>
                        <span class="text-xl font-semibold text-white">{{ $progress }}%</span>
                    </div>
                    <p class="mt-3 text-sm text-emerald-100/85">{{ $statusCopy[$status] ?? $statusCopy['not_started'] }}</p>
                    <dl class="mt-4 space-y-2 text-xs text-emerald-100/80">
                        <div class="flex items-center justify-between gap-4">
                            <dt>Submitted</dt>
                            <dd>{{ $submittedAt ? $submittedAt->format('M j, Y · g:i A') : '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Approved</dt>
                            <dd>{{ $approvedAt ? $approvedAt->format('M j, Y · g:i A') : 'Pending' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt>Attachments</dt>
                            <dd>{{ $pendingDocuments }} {{ \Illuminate\Support\Str::plural('file', $pendingDocuments) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <div class="grid gap-8 lg:grid-cols-3">
            <section class="space-y-6 lg:col-span-2">
                <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                    <header class="space-y-2 border-b border-slate-200 pb-4">
                        <h2 class="text-lg font-semibold text-[#0b3019]">Registration file</h2>
                        <p class="text-sm text-slate-500">Upload the official PDF from the registrar. It will replace any previous submission.</p>
                    </header>

                    <div class="mt-6 space-y-4 text-sm text-slate-600">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $statusLabels[$status] ?? \Illuminate\Support\Str::headline($status) }}
                            </span>
                            <span>{{ $submittedAt ? 'Uploaded '.$submittedAt->diffForHumans() : 'Awaiting upload' }}</span>
                        </div>

                        @if ($registration && count($documents))
                            <ul class="space-y-3">
                                @foreach ($documents as $path)
                                    @php($encoded = base64_encode($path))
                                    <li class="flex flex-col gap-2 rounded-2xl border border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3019]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path d="M4 4v12a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4V8l-6-6H8a4 4 0 0 0-4 4Z" />
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                            </svg>
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($path) }}" target="_blank" rel="noopener" class="max-w-[14rem] truncate font-semibold text-[#0b3019] hover:underline">
                                                {{ \Illuminate\Support\Str::afterLast($path, '/') }}
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('student.course-registration.documents.destroy', [$registration, $encoded]) }}" class="flex items-center justify-end">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                    <path d="M6 7h12" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                                    <path d="M21 7h-2l-1 12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 7H3" />
                                                </svg>
                                                Remove
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-6 text-center text-sm text-slate-500">
                                No registration PDF uploaded yet.
                            </div>
                        @endif
                    </div>
                </article>

                <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                    <header class="space-y-2 border-b border-slate-200 pb-4">
                        <h2 class="text-lg font-semibold text-[#0b3019]">Upload course registration PDF</h2>
                        <p class="text-sm text-slate-500">Download the pre-filled PDF from the registrar portal and upload it here. We already know your class and year.</p>
                    </header>

                    <form method="POST" action="{{ route('student.course-registration.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        <label class="flex flex-col gap-2">
                            <span class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Registration PDF</span>
                            <input type="file" name="registration_pdf" accept="application/pdf" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-full file:border-0 file:bg-[#0b3019] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#094018] focus:border-[#0b3019]/60 focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                            <span class="text-xs text-slate-400">Only PDF files up to 8 MB are accepted.</span>
                        </label>

                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 py-4 text-xs text-slate-500">
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M13 16h-1v-4h-1" />
                                    <path d="M12 8h.01" />
                                    <circle cx="12" cy="12" r="9" />
                                </svg>
                                <p>Uploading a new PDF overwrites the existing file. Ensure it is signed by your department before submitting.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M5 12L3 14l4 4 14-14-2-2-12 12z" />
                                </svg>
                                <p>We will confirm via email once the academic office approves your registration.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</a>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#0b3019] px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#094018] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M12 5v14" />
                                    <path d="m19 12-7 7-7-7" />
                                </svg>
                                Upload PDF
                            </button>
                        </div>
                    </form>
                </article>
            </section>

            <aside class="space-y-6">
                <article class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-[#0b3019]/5">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Timeline</h3>
                    <ol class="mt-4 space-y-4 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">1</span>
                            <div>
                                <p class="font-semibold text-slate-800">Download registrar PDF</p>
                                <p class="text-xs text-slate-500">Get the pre-filled registration form from the academic portal.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">2</span>
                            <div>
                                <p class="font-semibold text-slate-800">Sign & upload</p>
                                <p class="text-xs text-slate-500">Collect all required signatures and upload the PDF to ACSES.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">3</span>
                            <div>
                                <p class="font-semibold text-slate-800">Await approval</p>
                                <p class="text-xs text-slate-500">The faculty office reviews the PDF and emails the final decision.</p>
                            </div>
                        </li>
                    </ol>
                </article>

                <article class="rounded-3xl border border-[#0b3019]/10 bg-[#0b3019]/5 p-6 text-sm text-slate-700 shadow-lg shadow-[#0b3019]/10">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-[#0b3019]">Need help?</h3>
                    <p class="mt-3 text-sm">Contact ACSES academic services if you need assistance.</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-[#0b3019] shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M22 16.92a4 4 0 0 1-4 4A12 12 0 0 1 7.35 14.35 12 12 0 0 1 3.08 6a4 4 0 0 1 4-4h1.26a2 2 0 0 1 2 1.72 12.05 12.05 0 0 0 .7 2.7 2 2 0 0 1-.45 2.11L9 9a10 10 0 0 0 6 6l.47-.29a2 2 0 0 1 2.11-.45 12.05 12.05 0 0 0 2.7.7 2 2 0 0 1 1.72 2Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">Hotline</p>
                                <p class="text-xs text-slate-500">055 935 9824 · 08:00–20:00 GMT</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-[#0b3019] shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M3 8l9 6 9-6" />
                                    <path d="M21 8v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800">Email</p>
                                <p class="text-xs text-slate-500"><a href="mailto:acsesrepos@gmail.com" class="text-[#0b3019] underline-offset-4 hover:underline">acsesrepos@gmail.com</a></p>
                            </div>
                        </li>
                    </ul>
                </article>
            </aside>
        </div>
    </div>
</x-layouts.dashboard>
