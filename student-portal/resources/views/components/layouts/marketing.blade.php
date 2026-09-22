<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-sentry-head />
    <title>{{ trim($title ?? '') ? $title . ' | ' : '' }}{{ config('app.name', 'ACSES Portal') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-white via-slate-100 to-slate-200 text-slate-900">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-5 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-[#0b3019]">
                    <img src="{{ asset('logo.png') }}" alt="ACSES" class="h-10 w-10 rounded-xl border border-[#0b3019]/20 object-contain" loading="lazy">
                    <span class="text-base font-semibold">ACSES Portal</span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-[#0b3019]/20 bg-white px-4 py-2 text-sm font-semibold text-[#0b3019] shadow transition hover:-translate-y-0.5 hover:border-[#0b3019]/40">Sign in</a>
            </div>
        </header>

        <main class="flex-1">
            {{ $slot ?? '' }}
        </main>

        <footer class="border-t border-slate-200/80 bg-white/90 py-6">
            <div class="mx-auto w-full max-w-6xl px-5 text-center text-xs text-slate-500 sm:flex sm:items-center sm:justify-between sm:text-left">
                <p>© {{ now()->year }} ACSES. All rights reserved.</p>
                <div class="mt-3 flex justify-center gap-4 sm:mt-0 sm:justify-end">
                    <a href="{{ route('legal.terms') }}" class="transition hover:text-[#0b3019]">Terms</a>
                    <a href="{{ route('legal.privacy') }}" class="transition hover:text-[#0b3019]">Privacy</a>
                    <a href="{{ route('legal.cookies') }}" class="transition hover:text-[#0b3019]">Cookies</a>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
