@props(['title' => null, 'sidebar' => null, 'header' => null, 'footer' => null])

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

    <!-- FOUC prevention: apply theme before first paint -->
    <script>
        (function() {
            try {
                var t = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', t);
                if (t === 'dark') document.documentElement.classList.add('dark');
            } catch(e) {}
        })();
    </script>

    <!-- Instrument Sans — matches Tailwind @theme font-sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900" x-data="{ adminSidebarOpen: false, adminSidebarCollapsed: localStorage.getItem('admin-sidebar-collapsed') === 'true' }" x-on:admin-sidebar:toggle.window="adminSidebarOpen = !adminSidebarOpen" x-on:admin-sidebar:open.window="adminSidebarOpen = true" x-on:admin-sidebar:close.window="adminSidebarOpen = false" x-on:keydown.escape.window="adminSidebarOpen = false" :class="{ 'overflow-hidden': adminSidebarOpen }">
    <div class="flex min-h-screen w-full">
        @isset($sidebar)
            {{ $sidebar }}
        @endisset

        <div class="relative z-20 flex min-h-screen flex-1 flex-col">
            @if ($header)
                <div class="sticky top-0 z-40 shrink-0 bg-white/95 shadow-sm backdrop-blur supports-[backdrop-filter]:bg-white/75">
                    {{ $header }}
                </div>
            @else
                <div class="sticky top-0 z-40 shrink-0 bg-white/95 shadow-sm backdrop-blur supports-[backdrop-filter]:bg-white/75">
                    <x-dashboard.header />
                </div>
            @endif

            <main class="flex-1 pb-12 pt-4 sm:pt-6">
                <x-dues-lock-overlay>
                    {{ $slot ?? '' }}
                </x-dues-lock-overlay>
            </main>

            @if ($footer)
                <div class="mt-auto">
                    {{ $footer }}
                </div>
            @else
                <div class="mt-auto">
                    <x-dashboard.footer />
                </div>
            @endif
        </div>
    </div>

    @stack('scripts')
    <script src="https://unpkg.com/lucide@0.456.0/dist/umd/lucide.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons({
                    attrs: {
                        width: '1em',
                        height: '1em',
                        'stroke-width': '2'
                    }
                });
            }
        });
    </script>
</body>
</html>
