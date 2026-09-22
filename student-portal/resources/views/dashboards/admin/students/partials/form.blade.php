@props([
    'student',
    'isEdit' => false,
    'classOptions' => [],
    'yearOptions' => [],
])

@php
    $classOptions = collect($classOptions)
        ->merge([old('class', $student->class)])
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $yearOptions = collect($yearOptions)
        ->merge([old('year', $student->year)])
        ->filter()
        ->unique()
        ->sort()
        ->values();
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    <div class="space-y-2">
        <label for="username" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Username</label>
        <div class="relative">
            <i class="ri-at-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="username" type="text" name="username" value="{{ old('username', $student->username) }}" required class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
        </div>
        @error('username')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="fullname" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Full name</label>
        <div class="relative">
            <i class="ri-user-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="fullname" type="text" name="fullname" value="{{ old('fullname', $student->fullname) }}" required class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
        </div>
        @error('fullname')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="email" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Email</label>
        <div class="relative">
            <i class="ri-mail-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}" required class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
        </div>
        @error('email')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="phone_number" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Phone number</label>
        <div class="relative">
            <i class="ri-phone-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $student->phone_number) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
        </div>
        @error('phone_number')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="index_number" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Reference number</label>
        <div class="relative">
            <i class="ri-hashtag pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="index_number" type="text" name="index_number" value="{{ old('index_number', $student->index_number) }}" class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
        </div>
        @error('index_number')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="class" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Class</label>
        <div class="relative">
            <i class="ri-community-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <select id="class" name="class" class="h-12 w-full appearance-none rounded-2xl border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
                <option value="">Select class</option>
                @foreach ($classOptions as $option)
                    <option value="{{ $option }}" @selected((string) old('class', $student->class) === (string) $option)>{{ $option }}</option>
                @endforeach
            </select>
            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </div>
        @error('class')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="year" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Year</label>
        <div class="relative">
            <i class="ri-calendar-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <select id="year" name="year" class="h-12 w-full appearance-none rounded-2xl border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30">
                <option value="">Select</option>
                @forelse ($yearOptions as $option)
                    <option value="{{ $option }}" @selected((string) old('year', $student->year) === (string) $option)>Year {{ $option }}</option>
                @empty
                    @for ($y = 1; $y <= 6; $y++)
                        <option value="{{ $y }}" @selected(old('year', $student->year) == $y)>Year {{ $y }}</option>
                    @endfor
                @endforelse
            </select>
            <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </div>
        @error('year')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2" x-data="{
            password: '',
            message: '',
            helper: '',
            badgeClass: '',
            evaluate() {
                const value = this.password || '';
                if (! value.length) {
                    this.message = '';
                    this.helper = '';
                    this.badgeClass = '';
                    return;
                }

                let score = 0;
                if (value.length >= 8) score += 30;
                if (/[A-Z]/.test(value)) score += 15;
                if (/[a-z]/.test(value)) score += 15;
                if (/[0-9]/.test(value)) score += 20;
                if (/[^A-Za-z0-9]/.test(value)) score += 20;
                if (value.length >= 12) score += 10;

                if (score >= 85) {
                    this.message = 'Strong';
                    this.helper = 'Looks secure. Keep it safe!';
                    this.badgeClass = 'bg-emerald-100 text-emerald-700';
                } else if (score >= 60) {
                    this.message = 'Good';
                    this.helper = 'Add length or symbols for extra strength.';
                    this.badgeClass = 'bg-amber-100 text-amber-700';
                } else {
                    this.message = 'Weak';
                    this.helper = 'Use 8+ chars with mixed case, numbers & symbols.';
                    this.badgeClass = 'bg-rose-100 text-rose-700';
                }
            }
        }" x-init="$watch('password', () => evaluate())">
        <label for="password" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
            Password
            @if ($isEdit)
                <span class="text-slate-400">(leave blank to keep existing)</span>
            @endif
        </label>
        <div class="relative">
            <i class="ri-lock-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input x-model="password" id="password" type="password" name="password" class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30" @unless($isEdit) required @endunless>
        </div>
        @error('password')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
        <div class="flex items-center gap-2 text-xs" x-show="message" x-cloak>
            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold" :class="badgeClass">
                <i class="ri-shield-keyhole-line text-sm"></i>
                <span x-text="message"></span>
            </span>
            <span class="text-slate-500" x-text="helper"></span>
        </div>
    </div>

    <div class="space-y-2">
        <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Confirm password</label>
        <div class="relative">
            <i class="ri-lock-password-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="password_confirmation" type="password" name="password_confirmation" class="h-12 w-full rounded-2xl border border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-[#0b3019] focus:outline-none focus:ring-2 focus:ring-[#0b3019]/30" @unless($isEdit) required @endunless>
        </div>
    </div>

    @if ($isEdit)
        <input type="hidden" name="is_seller" value="{{ $student->is_seller ? 1 : 0 }}">
    @else
        <input type="hidden" name="is_seller" value="0">
    @endif
</div>
