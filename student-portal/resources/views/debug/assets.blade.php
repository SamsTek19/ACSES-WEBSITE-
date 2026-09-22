@php
    /**
     * Simple debug view for favicon and profile image assets.
     * Only use this in non-production or temporarily for diagnostics.
     */
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Debug</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto max-w-4xl px-4 py-8 space-y-8">
        <section class="rounded-2xl border border-[#0b3019]/20 bg-white p-6 shadow-sm shadow-[#0b3019]/10">
            <h1 class="text-xl font-semibold text-[#0b3019]">Asset Debug</h1>
            <p class="mt-2 text-sm text-slate-600">Environment-level information about favicons and profile images.</p>
            <dl class="mt-4 grid gap-4 text-sm text-slate-700 sm:grid-cols-2">
                <div>
                    <dt class="font-semibold text-slate-900">APP_URL</dt>
                    <dd class="mt-1 font-mono text-xs">{{ $debug['app_url'] ?? 'null' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Request URL</dt>
                    <dd class="mt-1 font-mono text-xs break-all">{{ $debug['request_url'] ?? 'null' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Public path</dt>
                    <dd class="mt-1 font-mono text-xs break-all">{{ $debug['public_path'] ?? 'null' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Storage public path</dt>
                    <dd class="mt-1 font-mono text-xs break-all">{{ $debug['storage_public_path'] ?? 'null' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Storage symlink exists</dt>
                    <dd class="mt-1 font-mono text-xs">{{ ($debug['storage_symlink_exists'] ?? false) ? 'true' : 'false' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Storage symlink is link</dt>
                    <dd class="mt-1 font-mono text-xs">{{ ($debug['storage_symlink_is_link'] ?? false) ? 'true' : 'false' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Favicons</h2>
            <p class="mt-1 text-sm text-slate-600">Each row shows whether the favicon file exists and what URL the app generates.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-3 py-2">Type</th>
                            <th class="px-3 py-2">File</th>
                            <th class="px-3 py-2">Exists</th>
                            <th class="px-3 py-2">Public path</th>
                            <th class="px-3 py-2">Asset URL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($favicons as $key => $info)
                            <tr>
                                <td class="px-3 py-2 font-mono text-xs">{{ $key }}</td>
                                <td class="px-3 py-2 font-mono text-xs">{{ $info['file'] }}</td>
                                <td class="px-3 py-2 font-mono text-xs">{{ $info['exists'] ? 'true' : 'false' }}</td>
                                <td class="px-3 py-2 font-mono text-xs break-all">{{ $info['public_path'] }}</td>
                                <td class="px-3 py-2 font-mono text-xs break-all">
                                    <a href="{{ $info['asset_url'] }}" class="text-[#0b3019] underline" target="_blank" rel="noopener noreferrer">{{ $info['asset_url'] }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Profile Images</h2>
            <p class="mt-1 text-sm text-slate-600">Resolved paths for the currently authenticated student/admin.</p>

            @forelse ($profiles as $role => $info)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                    <h3 class="text-sm font-semibold text-[#0b3019] uppercase tracking-[0.25em]">{{ strtoupper($role) }}</h3>
                    <dl class="mt-2 grid gap-3 text-xs text-slate-700 sm:grid-cols-2">
                        <div>
                            <dt class="font-semibold text-slate-900">User ID</dt>
                            <dd class="mt-1 font-mono">{{ $info['id'] ?? 'null' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Raw DB value</dt>
                            <dd class="mt-1 font-mono break-all">{{ $info['raw'] ?? 'null' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-900">Public disk exists</dt>
                            <dd class="mt-1 font-mono">{{ ($info['public_disk_exists'] ?? false) ? 'true' : 'false' }}</dd>
                        </div>
                        @if (!empty($info['asset_storage_url']))
                            <div>
                                <dt class="font-semibold text-slate-900">asset('storage/...')</dt>
                                <dd class="mt-1 font-mono break-all">
                                    <a href="{{ $info['asset_storage_url'] }}" class="text-[#0b3019] underline" target="_blank" rel="noopener noreferrer">{{ $info['asset_storage_url'] }}</a>
                                </dd>
                            </div>
                        @endif
                        @if (!empty($info['storage_url']))
                            <div>
                                <dt class="font-semibold text-slate-900">Storage::disk('public')->url()</dt>
                                <dd class="mt-1 font-mono break-all">
                                    <a href="{{ $info['storage_url'] }}" class="text-[#0b3019] underline" target="_blank" rel="noopener noreferrer">{{ $info['storage_url'] }}</a>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    @if (!empty($info['asset_storage_url']))
                        <div class="mt-3 flex items-center gap-3">
                            <div class="h-12 w-12 overflow-hidden rounded-full border border-[#0b3019]/30 bg-white">
                                <img src="{{ $info['asset_storage_url'] }}" alt="{{ strtoupper($role) }} profile preview" class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <p class="text-xs text-slate-500">Preview via <code>asset('storage/...')</code></p>
                        </div>
                    @endif
                </div>
            @empty
                <p class="mt-2 text-sm text-slate-500">No authenticated student/admin found for this request.</p>
            @endforelse
        </section>
    </main>
</body>
</html>
