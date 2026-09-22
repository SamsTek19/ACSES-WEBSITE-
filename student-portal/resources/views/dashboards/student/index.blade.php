<x-layouts.dashboard :title="$title">
    <div class="mx-auto w-full max-w-[1600px] px-5 py-8 sm:px-6 lg:px-8 relative">
        <div class="space-y-8 relative z-10">
            
            @php($duesAction = collect($quickActions ?? [])->firstWhere('label', 'Outstanding dues'))
            @php($nextEvent = ($events ?? collect())->first())

            <!-- Hero Section: Mobile (compact) -->
            <section class="sm:hidden overflow-hidden rounded-2xl border border-[#0b3019] bg-[#0b3019] px-5 py-4 text-white shadow-md animate-fade-slide">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-300">ACSES Portal</p>
                        <h1 class="mt-0.5 text-base font-bold tracking-tight truncate">
                            {{ $hero['greeting'] ?? 'Welcome back' }}, {{ $hero['first_name'] ?? 'Student' }}!
                        </h1>
                        @if ($duesAction)
                            <div class="mt-1.5 flex items-center gap-1.5 text-xs text-emerald-100/80">
                                <i data-lucide="wallet" class="h-3 w-3 text-emerald-400 shrink-0"></i>
                                <span>{{ $duesAction['state'] ?? 'Dues' }}{{ !empty($duesAction['value']) ? ' · ' . $duesAction['value'] : '' }}</span>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('student.dues.index') }}" class="shrink-0 flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25 active:scale-95">
                        <i data-lucide="wallet" class="h-4 w-4"></i>
                    </a>
                </div>
            </section>

            <!-- Hero Section: Desktop -->
            <section class="hidden sm:flex flex-col justify-between overflow-hidden rounded-[24px] border border-[#0b3019] bg-[#0b3019] p-8 text-white shadow-lg relative animate-fade-slide hover:shadow-[0_20px_40px_-15px_rgba(11,48,25,0.3)] transition-all duration-500">
                
                <div class="relative z-10 space-y-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-300">ACSES Student Portal</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight md:text-4xl">
                            {{ $hero['greeting'] ?? 'Welcome back' }}, {{ $hero['first_name'] ?? 'Student' }}!
                        </h1>
                        <p class="mt-3 max-w-2xl text-base text-emerald-50/80">
                            {{ $hero['message'] ?? 'Stay on top of your academic tasks, dues, and campus life in one place.' }}
                        </p>
                    </div>

                    @if ($duesAction || $nextEvent)
                        <div class="flex flex-wrap items-center gap-4 border-t border-emerald-800/50 pt-5">
                            @if ($duesAction)
                                <div class="flex items-center gap-2 text-sm text-emerald-100">
                                    <i data-lucide="wallet" class="h-4 w-4 text-emerald-400"></i>
                                    <span class="font-medium">{{ $duesAction['state'] ?? 'Dues' }}</span>
                                    @if (!empty($duesAction['value']))
                                        <span class="opacity-75">&middot; {{ $duesAction['value'] }}</span>
                                    @endif
                                </div>
                            @endif
                            @if ($nextEvent)
                                <div class="flex items-center gap-2 text-sm text-emerald-100">
                                    <i data-lucide="calendar" class="h-4 w-4 text-emerald-400"></i>
                                    <span class="font-medium">Next: {{ \Illuminate\Support\Str::limit($nextEvent['title'] ?? 'Event', 30) }}</span>
                                    @if (!empty($nextEvent['datetime']))
                                        <span class="opacity-75">&middot; {{ $nextEvent['datetime'] }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            <!-- Quick Actions Grid -->
            <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($quickActions as $index => $action)
                    @php($delayClass = $index === 0 ? 'animate-fade-slide-delay-200' : ($index === 1 ? 'animate-fade-slide-delay-400' : 'animate-fade-slide-delay-600'))
                    <article class="group relative overflow-hidden rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-[0_12px_30px_-10px_rgba(0,0,0,0.08)] hover:-translate-y-1 hover:border-[#0b3019]/30 animate-fade-slide {{ $delayClass }}">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-50 text-[#0b3019] group-hover:bg-[#0b3019]/5 group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="{{ $index === 0 ? 'wallet' : ($index === 1 ? 'calendar-check' : 'bell') }}" class="h-5 w-5"></i>
                            </div>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center justify-between gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <span>{{ $action['label'] ?? 'Action' }}</span>
                                    @if (!empty($action['state']))
                                        @php($stateLower = strtolower($action['state']))
                                        @php($badgeStyle = str_contains($stateLower, 'owing') || str_contains($stateLower, 'outstanding') ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' : (str_contains($stateLower, 'cleared') || str_contains($stateLower, 'paid') ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-slate-100 text-slate-600'))
                                        <span class="rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $badgeStyle }}">
                                            {{ $action['state'] }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $action['value'] ?? '--' }}</p>
                                <p class="text-sm text-slate-500">{{ $action['summary'] ?? '' }}</p>
                                
                                <div class="pt-3">
                                    <a href="{{ $action['cta_url'] ?? '#' }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#0b3019] transition hover:text-[#0b3019]/80">
                                        {{ $action['cta'] ?? 'View details' }}
                                        <i data-lucide="arrow-right" class="h-4 w-4 transform group-hover:translate-x-1.5 transition-transform duration-300"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <!-- Lower Dashboard Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Main Content Column -->
                <div class="space-y-6 lg:col-span-2">
                    
                    <!-- Calendar Section with Agenda Toggle -->
                    <section class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm animate-fade-slide animate-fade-slide-delay-400" x-data="{ calendarView: 'grid' }">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Department Calendar</h2>
                                <p class="text-sm text-slate-500">Upcoming classes, exams, and events.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs font-semibold">
                                    <button type="button" @click="calendarView = 'grid'" 
                                            :class="calendarView === 'grid' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                            class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 transition-all">
                                        <i data-lucide="grid" class="h-3.5 w-3.5"></i>
                                        <span>Month</span>
                                    </button>
                                    <button type="button" @click="calendarView = 'agenda'" 
                                            :class="calendarView === 'agenda' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                            class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 transition-all">
                                        <i data-lucide="list" class="h-3.5 w-3.5"></i>
                                        <span>Agenda</span>
                                    </button>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ $calendarMonthLabel }}
                                </span>
                            </div>
                        </div>

                        <!-- Grid View -->
                        <div x-show="calendarView === 'grid'" class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                                    <div class="px-2 py-3 text-center sm:text-left sm:px-4 sm:py-3">{{ $weekday }}</div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-7 text-sm">
                                @foreach ($calendarWeeks as $week)
                                    @foreach ($week as $day)
                                        <div class="flex min-h-[100px] flex-col border-b border-r border-slate-100 p-2 sm:p-3 {{ $day['is_current_month'] ? 'bg-white' : 'bg-slate-50/50 text-slate-400' }} hover:bg-[#0b3019]/[0.02] transition-all duration-200">
                                            <div class="flex justify-between items-center">
                                                <span class="hidden sm:inline-block text-[10px] font-semibold uppercase text-slate-400">{{ $day['month'] }}</span>
                                                <span @class([
                                                    'flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold transition-transform duration-200 hover:scale-110',
                                                    'bg-[#0b3019] text-white' => $day['is_today'],
                                                    'bg-emerald-50 text-emerald-700' => !$day['is_today'] && $day['is_current_month'] && !empty($day['has_upcoming']),
                                                    'text-slate-700' => !$day['is_today'] && !empty($day['is_current_month']) && empty($day['has_upcoming']),
                                                ])>
                                                    {{ $day['day'] }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2 space-y-1.5">
                                                @foreach ($day['events'] as $event)
                                                    <a href="{{ $event['cta_url'] ?? route('student.events.index') }}" 
                                                       class="group/evt relative block truncate rounded-md bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-700 transition-all duration-200 hover:bg-[#0b3019] hover:text-white"
                                                       title="{{ $event['title'] }}">
                                                        {{ $event['title'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <!-- Agenda View -->
                        <div x-show="calendarView === 'agenda'" x-cloak class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">
                            @php($allCalendarEvents = collect($calendarWeeks ?? [])->flatten(1)->pluck('events')->flatten(1)->unique('title'))
                            @forelse ($allCalendarEvents as $calEvt)
                                <div class="flex items-center justify-between p-4 transition hover:bg-slate-50">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#0b3019]">
                                            <i data-lucide="calendar-event" class="h-5 w-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-slate-900">{{ $calEvt['title'] }}</h4>
                                            <p class="text-xs text-slate-500">{{ $calEvt['datetime'] ?? 'Upcoming event' }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ $calEvt['cta_url'] ?? route('student.events.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0b3019] hover:underline">
                                        Details
                                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="p-8 text-center text-xs text-slate-400">No scheduled events found for this month.</div>
                            @endforelse
                        </div>
                    </section>

                    <!-- Academic Timeline -->
                    <section class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm animate-fade-slide animate-fade-slide-delay-600">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Academic Timeline</h2>
                                <p class="text-sm text-slate-500">Key dates and deadlines.</p>
                            </div>
                        </div>

                        <div class="relative ml-3 border-l-2 border-slate-100 py-2">
                            <ul class="space-y-8">
                                @forelse ($timelineEntries as $entry)
                                    <li class="relative pl-6 group">
                                        <!-- Timeline Dot -->
                                        <div @class([
                                            'absolute -left-[5px] top-1.5 h-2 w-2 rounded-full ring-4 ring-white transition-transform duration-300 group-hover:scale-125',
                                            'bg-[#0b3019]' => empty($entry['is_past']),
                                            'bg-slate-300' => !empty($entry['is_past']),
                                        ])></div>
                                        
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 transition-transform duration-200 group-hover:translate-x-1">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                    {{ $entry['date_label'] ?? ($entry['starts_at'] ?? 'TBA') }}
                                                </p>
                                                <h3 @class([
                                                    'mt-1 text-base font-semibold',
                                                    'text-slate-900' => empty($entry['is_past']),
                                                    'text-slate-500 line-through' => !empty($entry['is_past']),
                                                ])>
                                                    {{ $entry['title'] }}
                                                </h3>
                                            </div>
                                            @if (!empty($entry['cta_url']) && !empty($entry['cta_label']) && empty($entry['is_past']))
                                                <a href="{{ $entry['cta_url'] }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition-all duration-200 hover:bg-[#0b3019] hover:text-white hover:border-[#0b3019] active:scale-95">
                                                    {{ $entry['cta_label'] }}
                                                    <i data-lucide="external-link" class="h-3 w-3"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="pl-6 text-sm text-slate-500">No upcoming academic milestones.</li>
                                @endforelse
                            </ul>
                        </div>
                    </section>

                </div>

                <!-- Sidebar Column -->
                <div class="space-y-6">
                    
                    <!-- Security Tips -->
                    @php($tipCollection = $securityTips->take(5)->values())
                    @php($firstTip = $tipCollection->first())
                    
                    <section class="rounded-[24px] border border-slate-200 bg-[#0b3019]/[0.02] p-6 shadow-sm animate-fade-slide animate-fade-slide-delay-400" data-tip-slider data-tip-autoplay="5000" data-tip-tips='@json($tipCollection)'>
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="shield-alert" class="h-5 w-5 text-[#0b3019]"></i>
                            <h2 class="text-base font-bold text-slate-900">Security Advisory</h2>
                            <span data-tip-counter class="hidden"></span>
                        </div>
                        
                        @if ($tipCollection->isEmpty())
                            <p class="text-sm text-slate-500">No recent security advisories.</p>
                        @else
                            <div class="min-h-[120px] transition-all duration-200" data-tip-panel>
                                <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                    <span data-tip-category>{{ $firstTip['category'] ?? 'Security' }}</span>
                                    <span data-tip-published>{{ $firstTip['published'] ?? '' }}</span>
                                </div>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900 leading-snug" data-tip-title>{{ $firstTip['title'] ?? '' }}</h3>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-3" data-tip-excerpt>{{ $firstTip['excerpt'] ?? '' }}</p>
                            </div>
                            
                            <!-- Slider Dots -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex flex-wrap gap-1.5" data-tip-dots>
                                    @foreach ($tipCollection as $index => $tip)
                                        <button type="button" class="h-1.5 w-4 rounded-full transition-colors duration-200 {{ $index === 0 ? 'bg-[#0b3019]' : 'bg-slate-200' }}" data-tip-dot="{{ $index }}" aria-label="View tip"></button>
                                    @endforeach
                                </div>
                                <div class="flex gap-1 shrink-0 ml-2">
                                    <button type="button" data-tip-prev class="p-1 text-slate-400 hover:text-slate-700 transition"><i data-lucide="chevron-left" class="h-4 w-4"></i></button>
                                    <button type="button" data-tip-next class="p-1 text-slate-400 hover:text-slate-700 transition"><i data-lucide="chevron-right" class="h-4 w-4"></i></button>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Upcoming Events List -->
                    <section class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm animate-fade-slide animate-fade-slide-delay-600">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-base font-bold text-slate-900">Next Events</h2>
                            <a href="{{ route('student.events.index') }}" class="text-xs font-semibold text-[#0b3019] hover:underline">View all</a>
                        </div>
                        
                        <ul class="space-y-4">
                            @forelse ($events as $event)
                                <li class="flex items-start gap-3 hover:bg-slate-50 hover:-translate-y-0.5 hover:shadow-sm px-2 py-1.5 -mx-2 rounded-xl transition-all duration-200 group/item">
                                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-center group-hover/item:bg-[#0b3019]/5 group-hover/item:border-[#0b3019]/25 transition-all duration-200">
                                        <span class="text-[10px] font-bold uppercase text-slate-500">{{ $event['month_label'] ?? 'TBA' }}</span>
                                        <span class="text-sm font-bold text-slate-900 leading-tight">{{ $event['day_label'] ?? '--' }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="truncate text-sm font-semibold text-slate-900 group-hover/item:text-[#0b3019] transition-colors duration-200">{{ $event['title'] }}</h3>
                                        <p class="truncate text-xs text-slate-500">{{ $event['datetime'] ?? 'TBA' }}</p>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-500 text-center py-4">No upcoming events.</li>
                            @endforelse
                        </ul>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
