@php
    $title = 'Admin Dashboard';
@endphp

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-full px-6 py-8 sm:px-8 lg:px-10">
        <div class="space-y-6">

            <!-- Welcome Hero -->
            <section class="relative overflow-hidden rounded-2xl border border-[#0b3019]/20 bg-gradient-to-br from-[#0b3019] via-[#0d381e] to-[#072412] p-6 text-white shadow-md shadow-[#0b3019]/10 animate-fade-slide">
                <!-- Radial highlight top-right -->
                <div class="pointer-events-none absolute right-0 top-0 h-48 w-64 opacity-25"
                     style="background: radial-gradient(ellipse at 80% 0%, rgba(167,243,208,0.35) 0%, transparent 70%)"></div>
                <!-- Subtle grid texture -->
                <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
                     style="background-image: linear-gradient(rgba(255,255,255,.15) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.15) 1px, transparent 1px); background-size: 32px 32px;"></div>

                <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]" style="animation: pulse 2s infinite"></span>
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300">ACSES Control Center</p>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight sm:text-[1.625rem]">
                            {{ $hero['greeting'] ?? 'Welcome back' }}, {{ $adminName ?? 'Administrator' }}
                        </h1>
                        <p class="max-w-xl text-sm leading-relaxed text-white/65">
                            {{ $hero['message'] ?? 'Manage public content and review community feedback.' }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-[10px] font-semibold text-emerald-200/60">{{ now()->format('l, M j') }}</p>
                        <p class="text-lg font-bold tabular-nums text-white/90" id="admin-clock">{{ now()->format('g:i A') }}</p>
                    </div>
                </div>

                <!-- Quick-glance stat strip -->
                @if (!empty($overviewCards))
                <div class="relative z-10 mt-5 flex flex-wrap gap-2 border-t border-white/10 pt-4">
                    @foreach (collect($overviewCards)->take(3) as $qCard)
                        <a href="{{ $qCard['link'] ?? '#' }}" class="group flex items-center gap-2 rounded-xl border border-white/10 bg-white/8 px-3 py-1.5 backdrop-blur-sm transition-colors hover:border-white/20 hover:bg-white/12">
                            <i class="{{ $qCard['icon'] ?? 'ri-information-line' }} text-sm text-emerald-300 group-hover:text-emerald-200 transition-colors" aria-hidden="true"></i>
                            <span class="text-sm font-bold tabular-nums text-white">{{ $qCard['value'] }}</span>
                            <span class="text-[10px] font-medium text-white/55">{{ \Illuminate\Support\Str::limit($qCard['label'], 16) }}</span>
                        </a>
                    @endforeach
                </div>
                @endif
            </section>

            <!-- KPI Metrics Grid -->
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($overviewCards as $index => $card)
                    @php
                        $delays = ['animate-fade-slide', 'animate-fade-slide animate-fade-slide-delay-200', 'animate-fade-slide animate-fade-slide-delay-400', 'animate-fade-slide animate-fade-slide-delay-600'];
                        $delay  = $delays[$index] ?? 'animate-fade-slide';
                    @endphp

                    <article class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-slate-300 hover:shadow-md {{ $delay }}">
                        <div>
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1 space-y-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $card['label'] }}</p>
                                    <p class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">
                                        {{ $card['value'] }}
                                    </p>
                                </div>
                                <span class="ml-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-colors group-hover:bg-[#0b3019] group-hover:text-white">
                                    <i class="{{ $card['icon'] ?? 'ri-bar-chart-2-line' }} text-base" aria-hidden="true"></i>
                                </span>
                            </div>

                            <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ $card['description'] }}</p>
                        </div>

                        @if (!empty($card['link']))
                            <div class="mt-4 pt-3">
                                <a href="{{ $card['link'] }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0b3019] transition-all duration-150 hover:gap-1.5 hover:underline">
                                    <span>{{ $card['cta'] ?? 'View details' }}</span>
                                    <i class="ri-arrow-right-line text-sm" aria-hidden="true"></i>
                                </a>
                            </div>
                        @endif
                    </article>
                @endforeach
            </section>

        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var el = document.getElementById('admin-clock');
            if (!el) return;
            function tick() {
                var now = new Date();
                var h = now.getHours(), m = now.getMinutes();
                var ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                el.textContent = h + ':' + String(m).padStart(2, '0') + ' ' + ampm;
            }
            tick();
            setInterval(tick, 60000);
        })();

    </script>
    @endpush
</x-layouts.admin>
