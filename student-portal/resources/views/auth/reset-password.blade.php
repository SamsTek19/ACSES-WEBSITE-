@php($title = 'Reset password')

<x-layouts.auth :title="$title" card-width="max-w-lg">
    <div class="space-y-8">
        <div class="space-y-3 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#0b3019]/10 text-[#0b3019]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 5v14" />
                    <path d="M12 5v14" />
                    <path d="M18 5v14" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Set a new password</h1>
            <p class="text-sm text-slate-500">Create a strong password to secure your ACSES Portal account.</p>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-[#0b3019]/20 bg-[#0b3019]/10 px-4 py-3 text-sm text-[#0b3019]">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" data-auth-form>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-slate-700">New password</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]"
                        placeholder="Create a new password" />
                    <button type="button" data-password-toggle="#password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-slate-600" aria-label="Toggle password visibility">
                        <svg data-eye xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg data-eye-off xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m3 3 18 18" />
                            <path d="M10.584 10.59a1.999 1.999 0 0 0 2.828 2.83" />
                            <path d="M9.878 5.132A9.76 9.76 0 0 1 12 5c4.478 0 8.268 2.943 9.542 7a9.88 9.88 0 0 1-1.616 3.043m-4.112 2.773A9.711 9.711 0 0 1 12 19c-4.478 0-8.268-2.943-9.542-7a9.835 9.835 0 0 1 2.223-3.592" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]"
                        placeholder="Re-enter new password" />
                    <button type="button" data-password-toggle="#password_confirmation" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-slate-600" aria-label="Toggle password visibility">
                        <svg data-eye xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg data-eye-off xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m3 3 18 18" />
                            <path d="M10.584 10.59a1.999 1.999 0 0 0 2.828 2.83" />
                            <path d="M9.878 5.132A9.76 9.76 0 0 1 12 5c4.478 0 8.268 2.943 9.542 7a9.88 9.88 0 0 1-1.616 3.043m-4.112 2.773A9.711 9.711 0 0 1 12 19c-4.478 0-8.268-2.943-9.542-7a9.835 9.835 0 0 1 2.223-3.592" />
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="flex w-full items-center justify-center space-x-2 rounded-lg bg-[#0b3019] px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-[#094018] active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0b3019]">
                <span>Update password</span>
                <i data-lucide="arrow-right" class="text-sm" aria-hidden="true"></i>
            </button>
        </form>

        <div class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-semibold text-[#0b3019] hover:underline">Back to sign in</a>
        </div>
    </div>
</x-layouts.auth>
