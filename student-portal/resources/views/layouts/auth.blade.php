<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($title ?? '') ? $title . ' | ' : '' }}{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-white via-slate-100 to-slate-200 text-slate-900">
    <div class="relative flex min-h-screen flex-col">
        {{ $slot ?? '' }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorAll('[data-auth-form]');
            const overlay = document.getElementById('auth-loading-overlay');
            forms.forEach((form) => {
                form.addEventListener('submit', () => {
                    if (!overlay) {
                        return;
                    }
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                });
            });
        });
    </script>
</body>
</html>
