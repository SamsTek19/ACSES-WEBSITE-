@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $user = auth()->user();
    $firstName = $user ? Str::of($user->fullname ?? $user->username ?? $user->email)->trim()->explode(' ')->first() : 'Admin';
    $avatarInitials = Str::of($firstName)->substr(0, 1)->upper();
    $profileUrl = null;

    if ($user && $user->profile_picture) {
        $profileUrl = Str::startsWith($user->profile_picture, ['http://', 'https://'])
            ? $user->profile_picture
            : (Storage::disk('public')->exists($user->profile_picture)
                ? Storage::disk('public')->url($user->profile_picture)
                : null);
    }

    $profileRoute = Route::has('admin.profile') ? route('admin.profile') : null;
@endphp

<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur-md">
    <div class="flex items-center justify-between px-6 py-3">
        <!-- Left: Hamburger Toggle for Mobile, Elegant Breadcrumbs for Desktop -->
        <div class="flex items-center gap-3">
            <button type="button" x-data="{}" x-on:click="$dispatch('admin-sidebar:open')" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition-all duration-150 hover:border-[#0b3019]/40 hover:text-[#0b3019] active:scale-95 lg:hidden" aria-label="Open navigation" aria-controls="admin-mobile-sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M4 6h16" stroke-linecap="round" />
                    <path d="M4 12h16" stroke-linecap="round" />
                    <path d="M4 18h16" stroke-linecap="round" />
                </svg>
            </button>

            <!-- Breadcrumbs (Prime Desktop Real Estate) -->
            <div class="hidden lg:flex items-center gap-2 text-xs font-semibold text-slate-400">
                <span class="tracking-tight hover:text-slate-600 transition-colors">ACSES Portal</span>
                <i class="ri-arrow-right-s-line text-slate-300 text-sm"></i>
                <span class="text-[#0b3019] tracking-tight font-bold bg-[#0b3019]/5 px-2 py-0.5 rounded-md">Admin Console</span>
            </div>
        </div>

        <!-- Right: Interactive Profile Dropdown -->
        <div class="flex items-center gap-3" data-dropdown>
            <button type="button" data-dropdown-toggle="admin-profile-menu" class="group flex items-center gap-2 rounded-full p-0.5 transition-all duration-200 hover:ring-4 hover:ring-[#0b3019]/10 focus:outline-none">
                <span class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-[#0b3019]/8 border border-[#0b3019]/10 text-xs font-bold text-[#0b3019] group-hover:border-[#0b3019]/30 transition-colors">
                    @if ($profileUrl)
                        <img src="{{ $profileUrl }}" alt="{{ $firstName }} avatar" class="h-full w-full object-cover" loading="lazy">
                    @else
                        {{ $avatarInitials }}
                    @endif
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div id="admin-profile-menu" data-dropdown-menu class="invisible absolute right-6 top-full mt-2 w-56 translate-y-1 rounded-xl border border-slate-100 bg-white/95 backdrop-blur-md py-2 text-sm text-slate-600 opacity-0 shadow-lg shadow-slate-100/50 transition-all duration-200 ease-out z-50">
                <div class="flex items-center gap-3 px-4 pb-3 pt-1 border-b border-slate-50">
                    <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-[#0b3019]/10 text-xs font-bold text-[#0b3019]">
                        @if ($profileUrl)
                            <img src="{{ $profileUrl }}" alt="{{ $firstName }} avatar" class="h-full w-full object-cover" loading="lazy">
                        @else
                            {{ $avatarInitials }}
                        @endif
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $user->fullname ?? $firstName }}</p>
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0" title="Active session"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $user->email ?? 'admin@acses.edu' }}</p>
                    </div>
                </div>

                <div class="py-1 px-1">
                    @if ($profileRoute)
                        <a href="{{ $profileRoute }}" class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50 hover:text-[#0b3019] focus:bg-slate-50 focus:text-[#0b3019] focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-450" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5" />
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            </svg>
                            <span>Profile settings</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-1.5 text-left text-xs font-semibold text-rose-600 transition-colors hover:bg-rose-50/50 hover:text-rose-700 focus:bg-rose-50/50 focus:text-rose-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <path d="m16 17 5-5-5-5" />
                                <path d="M21 12H9" />
                            </svg>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
