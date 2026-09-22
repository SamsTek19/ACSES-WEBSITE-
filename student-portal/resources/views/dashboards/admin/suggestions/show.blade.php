@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $title = 'Suggestion · ' . ($suggestion->subject ?? 'Preview');
@endphp

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.suggestions.index') }}" class="hover:text-[#0b3019] transition-colors">Suggestions</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Detail view</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $suggestion->subject }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    @php
                        $status = strtolower($suggestion->status ?? 'pending');
                        $badgeMap = [
                            'pending' => 'bg-amber-50 text-amber-700',
                            'in_review' => 'bg-sky-50 text-sky-700',
                            'resolved' => 'bg-emerald-50 text-emerald-700',
                            'dismissed' => 'bg-rose-50 text-rose-600',
                        ];
                        $badgeClass = $badgeMap[$status] ?? 'bg-slate-100 text-slate-600';
                    @endphp
                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                        {{ $statuses[$suggestion->status] ?? Str::headline($suggestion->status) }}
                    </span>
                    <span class="text-xs text-slate-400">{{ $categories[$suggestion->category] ?? Str::headline($suggestion->category) }}</span>
                </div>
            </div>
            <a href="{{ route('admin.suggestions.index') }}" class="h-9 shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <i class="ri-arrow-left-line text-sm"></i>
                Back to suggestions
            </a>
        </header>

        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500 animate-fade-slide">
            <span class="inline-flex items-center gap-1.5">
                <i class="ri-user-line text-slate-400"></i>
                {{ $suggestion->user?->fullname ?? $suggestion->user?->username ?? 'Unknown student' }}
                @if ($suggestion->user?->email)
                    · {{ $suggestion->user?->email }}
                @endif
            </span>
            <span class="inline-flex items-center gap-1.5">
                <i class="ri-time-line text-slate-400"></i>
                Submitted {{ $suggestion->created_at?->format('M j, Y · g:i A') ?? '—' }}
            </span>
        </div>

        <!-- Message -->
        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
            <h2 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Message</h2>
            <div class="prose max-w-none whitespace-pre-line text-sm text-slate-700">
                {{ $suggestion->message }}
            </div>
        </section>

        <!-- Update status -->
        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
            <h2 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Update status</h2>
            <form method="POST" action="{{ route('admin.suggestions.update', $suggestion) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                @method('PUT')
                <div class="flex flex-col gap-1.5 sm:w-56">
                    <label for="status_select" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                    <div class="relative">
                        <select id="status_select" name="status" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($suggestion->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                    <i class="ri-save-line text-sm"></i>
                    Save changes
                </button>
            </form>
            @if ($suggestion->handled_at)
                <p class="mt-3 text-[10px] uppercase tracking-wider text-slate-400">Last handled {{ $suggestion->handled_at->format('M j, Y · g:i A') }}</p>
            @endif
        </section>

        @if ($suggestion->attachment_path)
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <h2 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Attachment</h2>
                <a href="{{ Storage::disk('public')->url($suggestion->attachment_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-[#0b3019]/20 px-3 py-2 text-xs font-semibold text-[#0b3019] transition hover:bg-[#0b3019]/5 active:scale-95">
                    <i class="ri-download-2-line text-sm"></i>
                    Download attachment
                </a>
            </section>
        @endif
    </div>
</x-layouts.admin>
