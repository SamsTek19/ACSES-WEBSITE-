@php($title = 'Admin Sign In')

<x-layouts.auth :title="$title">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#0b3019] text-white shadow-sm">
                <i data-lucide="shield-check" class="text-2xl" aria-hidden="true"></i>
            </div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#0b3019]">ACSES Administration</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Admin sign in</h1>
            <p class="mt-2 text-sm text-slate-500">Use your administrator credentials to continue.</p>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-xl border border-[#0b3019]/30 bg-[#0b3019]/10 px-4 py-3 text-sm text-[#0b3019]">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5" data-auth-form>
            @csrf
            <div class="space-y-1.5">
                <label for="login_id" class="block text-sm font-medium text-slate-700">Email, username, or index number</label>
                <input id="login_id" name="login_id" type="text" value="{{ old('login_id') }}" required autofocus autocomplete="username" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-300 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                @error('login_id')
                    <p class="text-xs text-red-500" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#0b3019] hover:underline">Forgot password?</a>
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-300 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]" />
                @error('password')
                    <p class="text-xs text-red-500" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex cursor-pointer items-center gap-2 text-sm text-slate-500">
                <input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]">
                <span>Remember me</span>
            </label>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#0b3019] px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#094018] focus:outline-none focus:ring-2 focus:ring-[#0b3019] focus:ring-offset-2">
                <span>Sign in to admin</span>
                <i data-lucide="arrow-right" class="text-sm" aria-hidden="true"></i>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Student access?
            <a href="{{ route('login') }}" class="font-medium text-[#0b3019] hover:underline">Go to student login</a>
        </p>
    </div>
</x-layouts.auth>
