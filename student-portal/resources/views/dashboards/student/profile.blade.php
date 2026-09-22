<x-layouts.dashboard :title="$title">
    @include('components.dashboard.skeleton-styles')

    @php
        $displayName = trim($student->fullname ?? $student->username ?? 'Student');
        $initials = collect(preg_split('/\s+/', $displayName))->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
        $croppedPreview = old('profile_picture_cropped');
        $storedImage = null;
        if ($student->profile_picture) {
            $storedImage = str_starts_with($student->profile_picture, 'http')
                ? $student->profile_picture
                : asset('storage/' . ltrim($student->profile_picture, '/'));
        }
        $profileImage = $croppedPreview ?: $storedImage;
        $removeRequested = old('remove_profile_picture') === '1';
        $hasProfileImage = (bool) ($profileImage && ! $removeRequested);
    @endphp

    @php
        $defaultTab = session('status') || $errors->any() ? 'profile_settings' : 'id_card';
    @endphp

    <div class="mx-auto w-full max-w-[1400px] px-4 py-8 sm:px-6 lg:px-8" x-data="{ activeTab: (window.location.hash ? window.location.hash.substring(1) : '{{ $defaultTab }}') }">
        
        <!-- Status Messages -->
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-950 shadow-sm animate-fade-slide">
                <div class="flex items-start gap-3">
                    <i class="ri-checkbox-circle-fill text-lg text-emerald-600 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Action Completed</p>
                        <p class="text-sm text-emerald-900 mt-0.5">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-950 shadow-sm animate-fade-slide">
                <div class="flex items-start gap-3">
                    <i class="ri-error-warning-fill text-lg text-rose-600 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-800">Please correct the following errors:</p>
                        <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs text-rose-900">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabbed Navigation Bar -->
        <div class="border-b border-slate-200 mb-8">
            <nav class="flex gap-2 overflow-x-auto whitespace-nowrap scrollbar-none" aria-label="Settings tabs">
                <button type="button" @click="activeTab = 'id_card'; window.location.hash = 'id_card'" 
                        :class="activeTab === 'id_card' ? 'border-[#0b3019] text-[#0b3019] font-bold bg-slate-50' : 'border-transparent text-slate-500 hover:text-slate-900 font-semibold hover:bg-slate-50/50'"
                        class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm transition-all rounded-t-lg">
                    <i class="ri-id-card-line text-base"></i>
                    <span>Digital ID Card</span>
                </button>
                <button type="button" @click="activeTab = 'profile_settings'; window.location.hash = 'profile_settings'" 
                        :class="activeTab === 'profile_settings' ? 'border-[#0b3019] text-[#0b3019] font-bold bg-slate-50' : 'border-transparent text-slate-500 hover:text-slate-900 font-semibold hover:bg-slate-50/50'"
                        class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm transition-all rounded-t-lg">
                    <i class="ri-user-settings-line text-base"></i>
                    <span>Personal Profile</span>
                </button>
                <button type="button" @click="activeTab = 'security_devices'; window.location.hash = 'security_devices'" 
                        :class="activeTab === 'security_devices' ? 'border-[#0b3019] text-[#0b3019] font-bold bg-slate-50' : 'border-transparent text-slate-500 hover:text-slate-900 font-semibold hover:bg-slate-50/50'"
                        class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm transition-all rounded-t-lg">
                    <i class="ri-shield-keyhole-line text-base"></i>
                    <span>Security & Devices</span>
                </button>
            </nav>
        </div>

        <!-- Main Form Scope -->
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" 
              class="space-y-8" data-profile-form>
            @csrf

            <!-- ================= TAB 1: DIGITAL ID CARD ================= -->
            <div x-show="activeTab === 'id_card'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-[0.99]" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">
                <div class="max-w-4xl mx-auto">
                    
                    <!-- Clean Minimal Digital ID Card -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-800 bg-[#0b3019] p-8 sm:p-10 text-white shadow-2xl">
                        
                        <!-- Top Bar / Header -->
                        <div class="flex items-center justify-between border-b border-white/10 pb-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-2 shadow-md shrink-0">
                                    <img src="{{ asset('logo.png') }}" alt="ACSES Logo" class="h-full w-full object-contain">
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-400">ACSES Student Member</p>
                                    <p class="text-sm font-semibold text-white/90">University of Mines & Technology</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="mt-8 flex flex-col items-center gap-8 md:flex-row md:items-start text-center md:text-left">
                            
                            <!-- Avatar Photo -->
                            <div class="relative shrink-0">
                                <div class="flex h-44 w-44 items-center justify-center overflow-hidden rounded-2xl border-2 border-white/20 bg-emerald-950/50 p-1 shadow-lg">
                                    @if ($hasProfileImage)
                                        <img data-avatar-preview src="{{ $profileImage }}" alt="{{ $displayName }} profile photo" class="h-full w-full rounded-xl object-cover" />
                                    @else
                                        <span data-avatar-fallback class="text-4xl font-bold uppercase text-emerald-300">{{ $initials ?: 'ST' }}</span>
                                    @endif
                                </div>
                                <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 md:left-auto md:right-3 md:translate-x-0 rounded-md bg-[#072011] px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400 border border-emerald-500/30 shadow-md">
                                    Verified
                                </div>
                            </div>

                            <!-- Student Placement & Complete Details -->
                            <div class="flex-1 w-full space-y-6">
                                <div>
                                    <h2 class="text-3xl font-extrabold tracking-tight text-white">{{ $student->fullname ?? 'Sample Student' }}</h2>
                                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-400 mt-1">
                                        @ {{ $student->username ?? 'student' }} &middot; Member Directory
                                    </p>
                                </div>

                                <!-- Complete Student Details Grid (Level omitted as requested) -->
                                <div class="grid grid-cols-1 gap-x-8 gap-y-3.5 text-xs text-emerald-100/90 sm:grid-cols-2 pt-2 border-t border-white/10">
                                    <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                        <i class="ri-hashtag text-emerald-400 text-base"></i>
                                        <span class="text-white/70">Index / Ref: <strong class="text-white font-semibold text-sm">{{ $student->index_number ?? 'Not assigned' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                        <i class="ri-graduation-cap-line text-emerald-400 text-base"></i>
                                        <span class="text-white/70">Programme: <strong class="text-white font-semibold text-sm">{{ $student->class ?? 'Not set' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                        <i class="ri-building-line text-emerald-400 text-base"></i>
                                        <span class="text-white/70">Department: <strong class="text-white font-semibold text-sm">{{ $student->department ?? 'Computer Science' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                        <i class="ri-phone-line text-emerald-400 text-base"></i>
                                        <span class="text-white/70">Phone: <strong class="text-white font-semibold text-sm">{{ $student->phone_number ?? 'Not set' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                        <i class="ri-[#0b3019] ri-shield-check-line text-emerald-400 text-base"></i>
                                        <span class="text-white/70">Account Status: <strong class="text-emerald-300 font-semibold text-sm">{{ $student->is_graduated ? 'Alumni' : 'Active Student' }}</strong></span>
                                    </div>
                                    @if ($student->is_seller)
                                        <div class="flex items-center gap-2.5 justify-center md:justify-start">
                                            <i class="ri-store-2-line text-emerald-400 text-base"></i>
                                            <span class="text-white/70">Marketplace: <strong class="text-amber-300 font-semibold text-sm">Verified Seller</strong></span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Email Footer -->
                                <div class="flex items-center gap-2.5 justify-center md:justify-start text-xs border-t border-white/10 pt-4 text-emerald-200/90 font-mono">
                                    <i class="ri-mail-line text-emerald-400 text-base"></i>
                                    <span class="text-sm">{{ $student->email }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Manage quick access button -->
                    <div class="mt-6 text-center">
                        <button type="button" @click="activeTab = 'profile_settings'" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                            <i class="ri-edit-line text-sm"></i>
                            <span>Edit Profile Contact Info</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: PROFILE SETTINGS ================= -->
            <div x-show="activeTab === 'profile_settings'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-[0.99]" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">
                
                <div class="grid gap-6 lg:grid-cols-3">
                    
                    <!-- Left: Profile avatar image update -->
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Profile Avatar</h3>
                        <div class="mt-6 flex flex-col items-center gap-4">
                            <div class="relative flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                                <img data-avatar-preview src="{{ $hasProfileImage ? $profileImage : '' }}" alt="{{ $displayName }} profile photo" class="{{ $hasProfileImage ? '' : 'hidden' }} h-full w-full object-cover" />
                                <span data-avatar-fallback class="{{ $hasProfileImage ? 'hidden' : '' }} text-2xl font-bold uppercase text-[#0b3019]">{{ $initials ?: 'ST' }}</span>
                            </div>
                            
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <button type="button" @click="document.querySelector('[data-avatar-input]').click()" data-avatar-trigger class="inline-flex h-8 items-center justify-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#082212] active:scale-95">
                                    <i class="ri-upload-cloud-line"></i>
                                    <span>Upload Photo</span>
                                </button>
                                <button type="button" data-avatar-remove class="{{ $hasProfileImage ? '' : 'hidden' }} inline-flex h-8 items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                    <i class="ri-delete-bin-6-line"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                            <p class="text-center text-[11px] text-slate-500 leading-normal max-w-xs" data-avatar-helper>Select a square image (minimum 400×400px). A crop dialog will assist you.</p>
                        </div>
                    </article>

                    <!-- Right: Contact form fields -->
                    <article class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contact Information</h3>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <!-- Primary Email -->
                            <label class="flex flex-col gap-1.5">
                                <span class="text-xs font-semibold text-slate-700">Primary Email Address</span>
                                <div class="relative">
                                    <i class="ri-mail-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="email" name="pending_email" id="pending_email" value="{{ old('pending_email', $student->pending_email ?? $student->email) }}" 
                                           class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-900 shadow-sm focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition-all">
                                </div>
                                
                                @if ($student->pending_email)
                                    <div class="mt-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                                        <p class="font-bold">Verification Pending</p>
                                        <p class="mt-0.5 leading-relaxed">Sent verification link to <strong>{{ $student->pending_email }}</strong>.</p>
                                    </div>
                                @endif
                            </label>

                            <!-- Phone Number -->
                            <label class="flex flex-col gap-1.5">
                                <span class="text-xs font-semibold text-slate-700">Phone Number</span>
                                <div class="relative">
                                    <i class="ri-phone-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="text" name="phone_number" value="{{ old('phone_number', $student->phone_number) }}" 
                                           class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-900 shadow-sm focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition-all">
                                </div>
                            </label>
                        </div>

                        <!-- Readonly Placements -->
                        <div class="pt-6 border-t border-slate-100 space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Academic Placement (Read-only)</h4>
                            <div class="grid gap-4 sm:grid-cols-3 text-xs">
                                <div>
                                    <span class="block font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Full Name</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-0.5 block">{{ $student->fullname }}</span>
                                </div>
                                <div>
                                    <span class="block font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Programme</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-0.5 block">{{ $student->class }}</span>
                                </div>
                                <div>
                                    <span class="block font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Level</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-0.5 block">Level {{ $student->year ? ($student->year . '00') : 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600 flex gap-2.5">
                                <i class="ri-information-line text-slate-400 text-base shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="font-semibold text-slate-800 block">Need to update official details?</span>
                                    <span class="mt-0.5 block text-slate-500 leading-normal">Official details (Name, Programme, and Level) are managed by administration. Please contact an executive if changes are needed.</span>
                                </div>
                            </div>
                        </div>

                    </article>
                </div>

                <input type="file" name="profile_picture" accept="image/*" class="hidden" data-avatar-input>
                <input type="hidden" name="profile_picture_cropped" value="{{ old('profile_picture_cropped') }}" data-avatar-cropped>
                <input type="hidden" name="remove_profile_picture" value="{{ $removeRequested ? '1' : '0' }}" data-avatar-remove-input>

                <!-- Action Button Controls -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="submit" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-[#0b3019] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#082212] active:scale-95">
                        <i class="ri-save-line"></i>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </div>

            <!-- ================= TAB 3: SECURITY & DEVICES ================= -->
            <div x-show="activeTab === 'security_devices'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-[0.99]" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">
                
                <div class="grid gap-6 md:grid-cols-2">
                    
                    <!-- Left: Password Change block -->
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between" x-data="{ showPass: false }">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                                    <i class="ri-lock-password-line text-lg"></i>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Security Credentials</h3>
                                    <p class="text-xs text-slate-500">Update account login password.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-xs leading-relaxed text-slate-500" x-show="!showPass">
                                Maintain security by updating your password periodically. Leave blank if unchanged.
                            </p>

                            <!-- Password Fields -->
                            <div x-show="showPass" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 space-y-3 pt-4 border-t border-slate-100">
                                
                                <label class="flex flex-col gap-1">
                                    <span class="text-xs font-semibold text-slate-700">Current Password</span>
                                    <input type="password" name="current_password" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                </label>

                                <label class="flex flex-col gap-1">
                                    <span class="text-xs font-semibold text-slate-700">New Password</span>
                                    <input type="password" name="password" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                </label>

                                <label class="flex flex-col gap-1">
                                    <span class="text-xs font-semibold text-slate-700">Confirm New Password</span>
                                    <input type="password" name="password_confirmation" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                </label>

                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="button" x-show="!showPass" @click="showPass = true" 
                                    class="inline-flex h-9 w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95">
                                <i class="ri-key-2-line"></i>
                                <span>Change password</span>
                            </button>
                            <button type="button" x-show="showPass" @click="showPass = false" 
                                    class="inline-flex h-9 w-1/2 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" x-show="showPass" 
                                    class="inline-flex h-9 w-1/2 items-center justify-center rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white transition hover:bg-[#082212]">
                                Save password
                            </button>
                        </div>
                    </article>

                    <!-- Right: Trusted Devices -->
                    <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                    <i class="ri-device-line text-lg"></i>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Trusted Devices</h3>
                                    <p class="text-xs text-slate-500">Authorized active devices.</p>
                                </div>
                            </div>

                            @if(isset($trustedDevices) && $trustedDevices->count() > 1)
                                <button type="submit" form="revoke-all-form" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                    <i class="ri-logout-circle-r-line"></i>
                                    <span>Sign out others</span>
                                </button>
                            @endif
                        </header>

                        <!-- Device List -->
                        <div class="mt-6 max-h-[220px] overflow-y-auto pr-1 scrollbar-thin">
                            @if(isset($trustedDevices) && $trustedDevices->count() > 0)
                                <ul class="divide-y divide-slate-100">
                                    @foreach($trustedDevices as $device)
                                        @php($isCurrent = isset($currentFingerprint) && $device->device_fingerprint === $currentFingerprint)
                                        <li class="flex items-center justify-between gap-4 py-2.5 px-2 rounded-lg transition hover:bg-slate-50 {{ $isCurrent ? 'bg-emerald-50/50' : '' }}">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $isCurrent ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                                    @if(str_contains(strtolower($device->device_name ?? ''), 'mobile') || str_contains(strtolower($device->device_name ?? ''), 'android') || str_contains(strtolower($device->device_name ?? ''), 'ios'))
                                                        <i class="ri-smartphone-line text-sm"></i>
                                                    @else
                                                        <i class="ri-computer-line text-sm"></i>
                                                    @endif
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-slate-900 truncate text-xs">{{ $device->device_name ?? 'Unknown Device' }}</span>
                                                        @if($isCurrent)
                                                            <span class="inline-flex items-center rounded bg-emerald-100 px-1.5 py-0.2 text-[9px] font-bold text-emerald-800 uppercase">Current</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                                        {{ $device->ip_address ?? 'Unknown IP' }} &middot; {{ $device->last_used_at ? $device->last_used_at->diffForHumans() : 'Active' }}
                                                    </p>
                                                </div>
                                            </div>
                                            @if(!$isCurrent)
                                                <button type="submit" form="revoke-device-{{ $device->id }}" class="text-slate-400 hover:text-rose-600 transition-colors p-1">
                                                    <i class="ri-close-circle-line text-base"></i>
                                                </button>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-8 text-xs text-slate-400">
                                    No trusted devices recorded yet.
                                </div>
                            @endif
                        </div>
                    </article>

                </div>
            </div>

            <!-- ================= PHOTO CROPPER MODALS & OVERLAYS ================= -->
            <div data-avatar-loading-overlay class="fixed inset-0 z-[65] hidden items-center justify-center bg-white/80 backdrop-blur-sm">
                <div class="flex flex-col items-center space-y-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full border border-[#0b3019]/20 bg-white shadow-md">
                        <i class="ri-loader-4-line animate-spin text-2xl text-[#0b3019]" aria-hidden="true"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Updating profile photo...</p>
                </div>
            </div>

            <div data-avatar-overlay class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-slate-950/60 px-4 py-10 backdrop-blur-sm md:px-8">
                <div data-avatar-editor class="relative mx-auto w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-xl md:p-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-1">
                            <h3 class="text-lg font-bold text-[#0b3019]">Adjust Profile Photo</h3>
                            <p class="text-xs text-slate-500">Drag the image to reposition, or zoom so your face fits within the border.</p>
                        </div>
                        <button type="button" data-avatar-cancel class="inline-flex h-8 w-8 items-center justify-center self-start rounded-lg border border-slate-200 text-slate-400 transition hover:text-[#0b3019] hover:bg-slate-50">
                            <span class="sr-only">Close crop dialog</span>
                            <i class="ri-close-line text-base" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="mt-6 flex max-h-[60vh] min-h-[300px] items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                        <img data-avatar-editor-image class="max-h-full w-full object-contain" alt="Adjust profile crop" />
                    </div>
                    <div data-avatar-controls class="mt-6 hidden flex flex-wrap items-center justify-end gap-2">
                        <button type="button" data-avatar-cancel class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="button" data-avatar-apply class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-[#0b3019] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#082212]">
                            <i class="ri-check-line text-sm" aria-hidden="true"></i>
                            Apply Crop
                        </button>
                    </div>
                </div>
            </div>

        </form>

        <!-- Hidden Device Revocation Forms -->
        @if(isset($trustedDevices))
            @foreach($trustedDevices as $device)
                <form id="revoke-device-{{ $device->id }}" method="POST" action="{{ route('student.profile.devices.revoke', $device) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
            
            <form id="revoke-all-form" method="POST" action="{{ route('student.profile.devices.revoke-all') }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif

    </div>
</x-layouts.dashboard>
