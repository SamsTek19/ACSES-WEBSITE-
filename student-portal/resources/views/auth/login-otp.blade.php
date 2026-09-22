@php
    $oldCode = old('code', '');
    $codeDigits = array_slice(array_pad(str_split($oldCode), 6, ''), 0, 6);
@endphp

<x-layouts.auth title="Verify New Device" card-width="max-w-md">
    <div class="space-y-6">
        <div class="space-y-1.5 text-center">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">New device detected</h1>
            <p class="text-xs leading-relaxed text-slate-500 px-4">
                We noticed a sign-in from a new device. Enter the 6-digit code sent to <span class="font-semibold text-slate-800">{{ $pending['email'] ?? 'your email' }}</span>.
            </p>
            @if (session('status'))
                <div class="mt-2 rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800 flex items-center gap-2 justify-center">
                    <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('auth.login.otp.submit') }}" class="space-y-5" data-auth-form>
            @csrf
            <div class="space-y-3" data-otp-container data-otp-target="#login-code">
                <input id="login-code" name="code" type="hidden" value="{{ $oldCode }}" required>
                
                <div class="flex justify-center gap-2">
                    @foreach ($codeDigits as $index => $digit)
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            autocomplete="{{ $index === 0 ? 'one-time-code' : 'off' }}"
                            data-otp-input
                            class="h-12 w-10 rounded-lg border border-slate-200 bg-slate-50/50 text-center text-lg font-bold text-slate-800 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] hover:bg-slate-50"
                            aria-label="Login code digit {{ $index + 1 }}"
                            value="{{ $digit }}"
                        >
                    @endforeach
                </div>

                @error('code')
                    <div class="rounded-lg bg-rose-50 border border-rose-100 p-3 flex items-start gap-2.5 text-rose-800">
                        <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 mt-0.5 text-rose-500"></i>
                        <div class="text-xs font-semibold leading-normal">{{ $message }}</div>
                    </div>
                @enderror
            </div>

            <button type="submit" class="w-full h-10 rounded-lg bg-[#0b3019] text-sm font-semibold text-white shadow-sm transition hover:bg-[#072412] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0b3019]">
                Verify device & continue
            </button>
        </form>

        <div class="pt-2 text-center text-xs text-slate-400">
            <span>Didn’t receive the code? </span>
            <form method="POST" action="{{ route('auth.login.otp.resend') }}" class="inline">
                @csrf
                <button type="submit" class="font-bold text-[#0b3019] hover:text-[#072412] hover:underline bg-transparent border-none p-0 inline-flex items-center gap-0.5 align-baseline">
                    Resend code
                </button>
            </form>
        </div>

        <div class="text-center text-xs">
            <a href="{{ route('login') }}" class="font-bold text-slate-500 hover:text-slate-800 flex items-center justify-center gap-1">
                <i data-lucide="arrow-left" class="h-3 w-3"></i>
                Back to sign in
            </a>
        </div>
    </div>
    </div>

    {{-- Rate limit handler script --}}
    {{-- Button loading state handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('[data-auth-form]');
            const overlay = document.getElementById('auth-loading-overlay');

            if (form && overlay) {
                form.addEventListener('submit', function() {
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                });
            }
        });
    </script>
</x-layouts.auth>
