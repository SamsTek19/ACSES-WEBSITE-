@props(['sections'])

<nav {{ $attributes->class(['flex flex-col gap-4 overflow-y-auto pr-1 scrollbar-none']) }}>
    @foreach ($sections as $section)
        <div class="space-y-1">
            <!-- Section Header -->
            @if(!empty($section['section']))
                <div class="flex items-center gap-1.5 px-3 mb-1" x-show="!adminSidebarCollapsed">
                    <span class="h-1 w-1 rounded-full bg-[#0b3019]/40"></span>
                    <h4 class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#0b3019]/60">
                        {{ $section['section'] }}
                    </h4>
                </div>
                <div class="border-t border-slate-100 my-1 mx-2" x-show="adminSidebarCollapsed"></div>
            @endif

            <div class="flex flex-col gap-1">
                @foreach ($section['items'] as $item)
                    <a href="{{ $item['href'] }}" 
                       @class([
                           'group flex items-center rounded-xl py-2 px-3 font-semibold text-xs transition-all duration-150 relative border',
                           'bg-[#0b3019]/8 border-[#0b3019]/15 text-[#0b3019] font-bold shadow-sm shadow-[#0b3019]/5' => $item['active'],
                           'border-transparent text-slate-600 hover:bg-slate-50 hover:text-[#0b3019]' => ! $item['active'],
                       ])
                       :class="adminSidebarCollapsed ? 'justify-center px-1' : 'gap-3 px-3'"
                       title="{{ $item['label'] }}"
                    >
                        <!-- Icon (inline, clean, no background box) -->
                        <i class="{{ $item['icon'] }} text-base shrink-0 transition-colors {{ $item['active'] ? 'text-[#0b3019]' : 'text-slate-400 group-hover:text-[#0b3019]' }}" aria-hidden="true"></i>
                        
                        <!-- Text Label -->
                        <span class="flex-1 tracking-tight truncate" x-show="!adminSidebarCollapsed">{{ $item['label'] }}</span>
                        
                        <!-- Badge -->
                        @if (!empty($item['badge']))
                            <span x-show="!adminSidebarCollapsed" @class([
                                'flex h-4 min-w-4 items-center justify-center rounded-full px-1.5 text-[9px] font-bold tracking-tight border',
                                'bg-[#0b3019]/15 border-[#0b3019]/10 text-[#0b3019]' => $item['active'],
                                'bg-rose-50 border-rose-100 text-rose-600' => ! $item['active'] && (str_contains($item['badge_color'] ?? '', 'rose') || str_contains($item['badge_color'] ?? '', 'red')),
                                'bg-amber-50 border-amber-100 text-amber-600' => ! $item['active'] && str_contains($item['badge_color'] ?? '', 'amber'),
                                'bg-emerald-50 border-emerald-100 text-emerald-600' => ! $item['active'] && (str_contains($item['badge_color'] ?? '', 'emerald') || str_contains($item['badge_color'] ?? '', 'green')),
                            ])>
                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                            </span>
                            <span x-show="adminSidebarCollapsed" class="absolute top-1 right-1 flex h-2 w-2 rounded-full border border-white {{ $item['badge_color'] ?? 'bg-rose-500' }}"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
