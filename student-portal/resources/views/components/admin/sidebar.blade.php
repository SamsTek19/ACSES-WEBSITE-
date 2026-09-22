@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // Admin user profile for sidebar footer
    $sidebarAdmin = auth()->guard('admin')->user() ?? auth()->user();
    $sidebarAdminName = $sidebarAdmin ? ($sidebarAdmin->fullname ?? $sidebarAdmin->username ?? 'Administrator') : 'Administrator';
    $sidebarAdminEmail = $sidebarAdmin?->email ?? '';
    $sidebarAdminInitial = Str::upper(Str::substr($sidebarAdminName, 0, 1));
    $sidebarAdminAvatar = null;
    if ($sidebarAdmin && $sidebarAdmin->profile_picture) {
        $pic = $sidebarAdmin->profile_picture;
        if (Str::startsWith($pic, ['http://', 'https://'])) {
            $sidebarAdminAvatar = $pic;
        } else {
            $path = ltrim($pic, '/');
            $sidebarAdminAvatar = Storage::disk('public')->exists($path) ? asset('storage/' . $path) : null;
        }
    }
@endphp
@php
    // Get pending registrations count
    try {
        $pendingRegistrationsCount = \App\Models\PendingRegistration::pending()->count();
    } catch (\Exception $e) {
        $pendingRegistrationsCount = 0;
    }

    // Get pending dues verifications count
    try {
        $pendingDuesCount = \App\Models\Due::where('payment_status', 'pending_verification')
            ->where('payment_method', 'manual')
            ->count();
    } catch (\Exception $e) {
        $pendingDuesCount = 0;
    }

    $navConfig = [
        [
            'section' => 'Core',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route_name' => 'admin.dashboard',
                    'pattern' => 'admin.dashboard',
                    'icon' => 'ri-layout-grid-line',
                ],
                [
                    'label' => 'Pending Registrations',
                    'route_name' => 'admin.pending-registrations.index',
                    'pattern' => 'admin.pending-registrations.*',
                    'icon' => 'ri-user-received-2-line',
                    'badge' => $pendingRegistrationsCount > 0 ? $pendingRegistrationsCount : null,
                    'badge_color' => 'bg-amber-500',
                    'href' => url('/admin/pending-registrations'),
                ],
                [
                    'label' => 'Students',
                    'route_name' => 'admin.students.index',
                    'pattern' => 'admin.students.*',
                    'icon' => 'ri-team-line',
                ],
            ]
        ],
        [
            'section' => 'Finance',
            'items' => [
                [
                    'label' => 'Dues & Fees',
                    'route_name' => 'admin.dues.index',
                    'pattern' => 'admin.dues.index',
                    'icon' => 'ri-coins-line',
                ],
                [
                    'label' => 'Verifications',
                    'route_name' => 'admin.dues.verifications.index',
                    'pattern' => 'admin.dues.verifications.*',
                    'icon' => 'ri-file-shield-2-line',
                    'badge' => $pendingDuesCount > 0 ? $pendingDuesCount : null,
                    'badge_color' => 'bg-emerald-600',
                    'href' => url('/admin/dues/verifications'),
                ],
            ]
        ],
        [
            'section' => 'Content & Academic',
            'items' => [
                [
                    'label' => 'Events',
                    'route_name' => 'admin.events.index',
                    'pattern' => 'admin.events.*',
                    'icon' => 'ri-calendar-event-line',
                ],
                [
                    'label' => 'Announcements',
                    'route_name' => 'admin.announcements.index',
                    'pattern' => 'admin.announcements.*',
                    'icon' => 'ri-megaphone-line',
                ],
                [
                    'label' => 'Timeline',
                    'route_name' => 'admin.timeline.index',
                    'pattern' => 'admin.timeline.*',
                    'icon' => 'ri-time-line',
                ],
                [
                    'label' => 'Resources',
                    'route_name' => 'admin.resources.index',
                    'pattern' => 'admin.resources.*',
                    'icon' => 'ri-book-read-line',
                ],
                [
                    'label' => 'Feedback',
                    'route_name' => 'admin.suggestions.index',
                    'pattern' => 'admin.suggestions.*',
                    'icon' => 'ri-message-3-line',
                ],
            ]
        ],
        [
            'section' => 'Account',
            'items' => [
                [
                    'label' => 'Profile & Settings',
                    'route_name' => 'admin.profile',
                    'pattern' => 'admin.profile*',
                    'icon' => 'ri-settings-3-line',
                ],
            ]
        ]
    ];

    $sections = collect($navConfig)->map(function ($section) {
        $items = collect($section['items'])->map(function ($item) {
            if (!empty($item['href'])) {
                $href = $item['href'];
            } else {
                $routeName = $item['route_name'] ?? null;
                $href = ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) ? route($routeName) : '#';
            }

            return [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'href' => $href,
                'active' => ! empty($item['pattern']) ? request()->routeIs($item['pattern']) : false,
                'badge' => $item['badge'] ?? null,
                'badge_color' => $item['badge_color'] ?? 'bg-rose-500',
            ];
        })->toArray();

        return [
            'section' => $section['section'],
            'items' => $items,
        ];
    })->toArray();
@endphp

<aside
    class="hidden shrink-0 border-r border-slate-100 bg-white text-sm text-slate-600 lg:flex lg:flex-col lg:sticky lg:top-0 lg:h-screen transition-all duration-300 relative select-none"
    :class="adminSidebarCollapsed ? 'w-16 px-2 py-4' : 'w-64 px-4 py-5'"
    aria-label="Admin navigation"
>
    <div class="flex h-full flex-col justify-between overflow-hidden">
        <!-- Top Section: Logo Header + Nav -->
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Sidebar Header with Collapse Button -->
            <div class="flex items-center justify-between text-[#0b3019] shrink-0 pb-4 mb-2 border-b border-slate-100" :class="adminSidebarCollapsed ? 'justify-center pb-3' : 'px-1'">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('logo.png') }}" alt="ACSES" class="h-9 w-9 rounded-xl border border-[#0b3019]/20 object-contain shrink-0" loading="lazy">
                    <div x-show="!adminSidebarCollapsed" x-transition:enter="transition-opacity duration-200" class="min-w-0">
                        <p class="text-[9px] font-bold uppercase tracking-[0.25em] text-[#0b3019]/70 truncate">ACSES Admin</p>
                        <p class="text-base font-bold text-[#0b3019] truncate leading-tight">Control Center</p>
                    </div>
                </div>

                <!-- Toggle Collapse Button -->
                <button type="button" 
                        @click="adminSidebarCollapsed = !adminSidebarCollapsed; localStorage.setItem('admin-sidebar-collapsed', adminSidebarCollapsed)"
                        class="hidden lg:flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200/80 bg-slate-50/50 text-slate-400 hover:border-[#0b3019]/30 hover:bg-[#0b3019]/5 hover:text-[#0b3019] transition-all duration-150 shrink-0"
                        :title="adminSidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                    <i class="text-sm transition-transform duration-300" :class="adminSidebarCollapsed ? 'ri-indent-increase' : 'ri-indent-decrease'"></i>
                </button>
            </div>

            <!-- Nav Container -->
            <div class="flex-1 min-h-0 overflow-y-auto scrollbar-none py-1">
                <x-admin.sidebar-nav :sections="$sections" class="flex-1" />
            </div>
        </div>

        <!-- Admin Profile Footer (Fixed at bottom) -->
        <div class="border-t border-slate-100 pt-2.5 shrink-0">
            <div class="flex items-center rounded-lg hover:bg-slate-50 transition-colors duration-150 group"
                 :class="adminSidebarCollapsed ? 'justify-center p-1' : 'gap-2.5 px-2 py-1.5'">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full border border-[#0b3019]/15 bg-[#0b3019]/8 text-[11px] font-bold text-[#0b3019]">
                    @if ($sidebarAdminAvatar)
                        <img src="{{ $sidebarAdminAvatar }}" alt="{{ $sidebarAdminName }}" class="h-full w-full object-cover">
                    @else
                        {{ $sidebarAdminInitial }}
                    @endif
                </span>
                <div class="min-w-0 flex-1" x-show="!adminSidebarCollapsed">
                    <p class="truncate text-xs font-bold leading-tight text-slate-800">{{ Str::limit($sidebarAdminName, 15) }}</p>
                    <p class="truncate text-[9px] leading-none text-slate-400 mt-0.5">{{ Str::limit($sidebarAdminEmail, 18) }}</p>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}" class="shrink-0" x-show="!adminSidebarCollapsed">
                    @csrf
                    <button type="submit" class="flex h-6 w-6 items-center justify-center rounded text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600 active:scale-95" title="Sign out">
                        <i class="ri-logout-box-r-line text-xs" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            
            <div x-show="adminSidebarCollapsed" class="mt-1 flex justify-center">
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-md border border-slate-100 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600 active:scale-95" title="Sign out">
                        <i class="ri-logout-box-r-line text-xs" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<div class="lg:hidden">
    <div x-show="adminSidebarOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60" aria-hidden="true" @click="adminSidebarOpen = false"></div>

    <div id="admin-mobile-sidebar" x-show="adminSidebarOpen" style="display: none;" x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-60 overflow-y-auto border-r border-slate-100 bg-white px-4 py-5 text-sm text-slate-600 shadow-xl">
        <div class="flex items-center justify-between text-[#0b3019]">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('logo.png') }}" alt="ACSES" class="h-8 w-8 rounded-lg border border-[#0b3019]/20 object-contain" loading="lazy">
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-[0.25em] text-[#0b3019]/70">Admin</p>
                    <p class="text-sm font-bold text-[#0b3019]">Control Center</p>
                </div>
            </div>
            <button type="button" class="rounded-full border border-slate-200/70 p-1.5 text-slate-500 transition hover:text-[#0b3019]" aria-label="Close sidebar" @click="adminSidebarOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="m6 6 12 12" />
                    <path d="m6 18 12-12" />
                </svg>
            </button>
        </div>

        <x-admin.sidebar-nav :sections="$sections" class="mt-4 flex-1" />

        <div class="mt-4 border-t border-slate-100 pt-3">
            <div class="flex items-center gap-2.5 rounded-lg px-2 py-1.5 hover:bg-slate-50 transition-colors duration-150">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full border border-[#0b3019]/15 bg-[#0b3019]/8 text-[11px] font-bold text-[#0b3019]">
                    @if ($sidebarAdminAvatar)
                        <img src="{{ $sidebarAdminAvatar }}" alt="{{ $sidebarAdminName }}" class="h-full w-full object-cover">
                    @else
                        {{ $sidebarAdminInitial }}
                    @endif
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold leading-tight text-slate-800">{{ Str::limit($sidebarAdminName, 15) }}</p>
                    <p class="truncate text-[9px] leading-none text-slate-400 mt-0.5">{{ Str::limit($sidebarAdminEmail, 18) }}</p>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex h-6 w-6 items-center justify-center rounded text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600 active:scale-95" title="Sign out">
                        <i class="ri-logout-box-r-line text-xs" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
