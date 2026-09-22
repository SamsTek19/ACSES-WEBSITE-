@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isAdminRoute = request()->routeIs('admin.*');

    $guard = $isAdminRoute ? 'admin' : 'student';
    $user = auth()->guard($guard)->user();

    $rawName = $user ? ($user->username ?: ($user->fullname ?: $user->email)) : 'student';
    $displayName = Str::of($rawName)->trim();
    $avatarInitials = Str::of($displayName)->substr(0, 1)->upper();
    $avatarUrl = null;

    if ($user && $user->profile_picture) {
        if (Str::startsWith($user->profile_picture, ['http://', 'https://'])) {
            $avatarUrl = $user->profile_picture;
        } else {
            $path = ltrim($user->profile_picture, '/');
            $avatarUrl = asset('storage/' . $path);
        }
    }
@endphp

<header class="sticky top-0 z-40 w-full border-b border-slate-100 bg-white/80 backdrop-blur-md shadow-sm">
    <!-- Actual header content -->
    <div class="mx-auto flex w-full max-w-[1600px] items-center justify-between px-5 py-3 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Left: Branding & Mobile Menu Toggle -->
        <div class="flex items-center gap-4">
            @if ($isAdminRoute)
                <button type="button" x-data="{}" x-on:click="$dispatch('admin-sidebar:open')" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 md:hidden" aria-label="Open navigation" aria-controls="admin-mobile-sidebar">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
            @endif
            <a href="{{ $isAdminRoute ? route('admin.dashboard') : route('student.dashboard') }}" class="flex items-center gap-2.5 group/logo">
                <img src="{{ asset('logo.png') }}" alt="ACSES Logo" class="h-8 w-8 rounded-lg object-contain transition-transform duration-300 group-hover/logo:scale-105" fetchpriority="high">
                <span class="text-[15px] font-bold tracking-tight text-slate-900 hidden sm:block transition-colors duration-300 group-hover/logo:text-[#0b3019]">
                    {{ $isAdminRoute ? 'ACSES Admin' : 'ACSES Portal' }}
                </span>
            </a>
        </div>

        <!-- Right: Actions & Profile -->
        <div class="flex items-center gap-3">
            
            <!-- Quick Navigation Dropdown (Desktop) -->
            <div class="relative hidden md:block" data-dropdown>
                <button type="button" data-dropdown-toggle="nav-menu" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition-all duration-300 hover:bg-slate-50 hover:border-[#0b3019]/40 hover:text-[#0b3019] active:scale-95 group/menu-btn" aria-label="Open menu">
                    <i data-lucide="menu" class="h-4 w-4 transition-transform duration-300 group-hover/menu-btn:scale-105"></i>
                </button>
                <div id="nav-menu" data-dropdown-menu class="invisible absolute right-0 mt-2 w-56 translate-y-1 rounded-2xl border border-slate-200 bg-white p-2 text-sm text-slate-600 opacity-0 shadow-lg transition duration-200 ease-out">
                    <div class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Menu</div>
                    <a href="{{ $isAdminRoute ? route('admin.dashboard') : route('student.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="layout-dashboard" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Dashboard
                    </a>
                    <a href="https://store.acses-umat.com/" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="shopping-bag" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        ACSES Store
                    </a>
                    <a href="{{ route('student.announcements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="megaphone" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Announcements
                    </a>
                    <a href="{{ route('student.events.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="calendar" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Events
                    </a>
                    <a href="{{ route('student.dues.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="wallet" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Dues
                    </a>
                    <a href="{{ route('student.resources.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="book-open" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Resources
                    </a>
                    <a href="{{ route('student.suggestions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/item">
                        <i data-lucide="message-square" class="h-4 w-4 text-slate-400 group-hover/item:text-[#0b3019]/70 group-hover/item:scale-105 transition-all duration-200"></i>
                        Suggestion Box
                    </a>
                </div>
            </div>

            <!-- Profile Dropdown (Desktop) -->
            <div class="relative hidden max-w-[240px] md:block" data-dropdown>
                <button type="button" data-dropdown-toggle="profile-menu" class="flex items-center gap-2.5 rounded-full border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 text-sm font-medium text-slate-700 shadow-sm transition-all duration-300 hover:bg-slate-50 hover:border-[#0b3019]/40 active:scale-95 group/profile-btn">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-xs font-bold text-slate-600 transition-transform duration-300 group-hover/profile-btn:scale-105">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover">
                        @else
                            {{ $avatarInitials }}
                        @endif
                    </span>
                    <span class="max-w-[120px] truncate">{{ $displayName }}</span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-300 group-hover/profile-btn:translate-y-0.5"></i>
                </button>
                <div id="profile-menu" data-dropdown-menu class="invisible absolute right-0 mt-2 w-48 translate-y-1 rounded-2xl border border-slate-200 bg-white p-2 text-sm text-slate-600 opacity-0 shadow-lg transition duration-200 ease-out">
                    <a href="{{ route('student.profile') }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/profile-item">
                        <i data-lucide="user" class="h-4 w-4 text-slate-400 group-hover/profile-item:text-[#0b3019]/70 group-hover/profile-item:scale-105 transition-all duration-200"></i>
                        My Profile
                    </a>
                    <div class="my-1 h-px bg-slate-100"></div>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-rose-600 transition-all duration-200 hover:bg-rose-50 hover:text-rose-700 active:scale-95 group/logout-item">
                            <i data-lucide="log-out" class="h-4 w-4 transition-transform duration-200 group-hover/logout-item:translate-x-0.5"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Navigation & Profile Menu -->
            <div class="relative md:hidden" data-dropdown>
                <button type="button" data-dropdown-toggle="mobile-nav-menu" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition-all duration-300 hover:bg-slate-50 hover:border-[#0b3019]/40 active:scale-95 group/mobile-btn" aria-label="Open menu">
                    <i data-lucide="menu" class="h-4 w-4"></i>
                </button>
                <div id="mobile-nav-menu" data-dropdown-menu class="invisible absolute right-0 mt-2 w-64 translate-y-1 rounded-2xl border border-slate-200 bg-white p-2 text-sm text-slate-600 opacity-0 shadow-lg transition duration-200 ease-out">
                    
                    <!-- Mobile Profile Info -->
                    <div class="flex items-center gap-3 border-b border-slate-100 px-3 pb-3 pt-2">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover">
                            @else
                                {{ $avatarInitials }}
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900">{{ $displayName }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $user->email ?? 'student@acses.edu' }}</p>
                        </div>
                    </div>

                    <!-- Mobile Navigation Links -->
                    <div class="mt-2 space-y-1">
                        <a href="{{ $isAdminRoute ? route('admin.dashboard') : route('student.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="layout-dashboard" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Dashboard
                        </a>
                        <a href="https://store.acses-umat.com/" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="shopping-bag" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            ACSES Store
                        </a>
                        <a href="{{ route('student.announcements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="megaphone" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Announcements
                        </a>
                        <a href="{{ route('student.events.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="calendar" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Events
                        </a>
                        <a href="{{ route('student.dues.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="wallet" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Dues
                        </a>
                        <a href="{{ route('student.resources.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="book-open" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Resources
                        </a>
                        <a href="{{ route('student.suggestions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="message-square" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            Suggestion Box
                        </a>
                        <a href="{{ route('student.profile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200 hover:bg-[#0b3019]/5 hover:text-[#0b3019] group/mobile-item">
                            <i data-lucide="user" class="h-4 w-4 text-slate-400 group-hover/mobile-item:text-[#0b3019]/70 group-hover/mobile-item:scale-105 transition-all duration-200"></i>
                            My Profile
                        </a>
                    </div>
                    
                    <div class="my-2 h-px bg-slate-100"></div>
                    
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-rose-600 transition-all duration-200 hover:bg-rose-50 hover:text-rose-700 active:scale-95 group/logout-item">
                            <i data-lucide="log-out" class="h-4 w-4 transition-transform duration-200 group-hover/logout-item:translate-x-0.5"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</header>
