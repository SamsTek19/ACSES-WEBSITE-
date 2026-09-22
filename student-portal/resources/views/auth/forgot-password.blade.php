@php($title = 'Forgot password')

<x-layouts.auth :title="$title" card-width="max-w-lg">
    <div class="space-y-8">
        <div class="space-y-3 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#0b3019]/10 text-[#0b3019]">
                <i data-lucide="help-circle" class="text-3xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Forgot your password?</h1>
            <p class="text-sm text-slate-500">
                Enter your email address and we will send you a secure link to reset your password.
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-[#0b3019]/20 bg-[#0b3019]/10 px-4 py-3 text-sm text-[#0b3019]">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-auth-form>
            @csrf
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="mail" class="text-base" aria-hidden="true"></i>
                    </span>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}"
                        class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]"
                        placeholder="you@example.com" />
                </div>
                @error('email')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="flex w-full items-center justify-center space-x-2 rounded-lg bg-[#0b3019] px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-[#094018] active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0b3019]">
                <span>Send reset link</span>
                <i data-lucide="arrow-right" class="text-sm" aria-hidden="true"></i>
            </button>
        </form>

        <div class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-semibold text-[#0b3019] hover:underline">Back to sign in</a>
        </div>
    </div>
</x-layouts.auth>
