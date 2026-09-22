@php($title = 'Admin profile')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl space-y-6 px-5 py-10 sm:px-6 lg:px-8">

        <!-- Header -->
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <i class="ri-user-settings-line text-sm"></i>
                    <span>Admin profile</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Administrator workspace</h1>
                <p class="text-sm text-slate-500">Manage your profile, invite other administrators, and generate system snapshots.</p>
            </div>
            <div class="shrink-0 rounded-xl border border-slate-100 bg-white px-4 py-2.5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Signed in as</p>
                <p class="mt-0.5 text-sm font-bold text-slate-900">{{ $admin->fullname ?? $admin->username }}</p>
                <p class="text-xs text-slate-400">{{ $admin->email }}</p>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
                <div class="flex items-center gap-2">
                    <i class="ri-check-double-line text-base text-emerald-600"></i>
                    <p>{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 animate-fade-slide">
                <div class="flex items-center gap-2 mb-1.5">
                    <i class="ri-error-warning-line text-base text-rose-500"></i>
                    <p class="font-semibold">Please review the highlighted fields:</p>
                </div>
                <ul class="ml-6 list-disc space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile + Invite grid -->
        <section class="grid gap-5 lg:grid-cols-2 animate-fade-slide animate-fade-slide-delay-200">

            <!-- Profile settings -->
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0b3019]/8 text-[#0b3019]">
                        <i class="ri-user-settings-line text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Profile settings</h2>
                        <p class="text-xs text-slate-400">Update contact details and password.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="fullname" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Full name</label>
                            <input id="fullname" name="fullname" type="text" value="{{ old('fullname', $admin->fullname) }}" autocomplete="name" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="username" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Username</label>
                            <input id="username" name="username" type="text" value="{{ old('username', $admin->username) }}" required autocomplete="username" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $admin->email) }}" required autocomplete="email" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="phone_number" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Phone</label>
                            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $admin->phone_number) }}" autocomplete="tel" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="password" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">New password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                            <p class="text-[10px] text-slate-400">Leave blank to keep current password.</p>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                            <i class="ri-save-3-line text-sm"></i>
                            Save changes
                        </button>
                    </div>
                </form>
            </article>

            <!-- Invite administrator -->
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0b3019]/8 text-[#0b3019]">
                        <i class="ri-user-add-line text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Invite administrator</h2>
                        <p class="text-xs text-slate-400">Provision a new admin with console access.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.admins.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_fullname" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Full name</label>
                            <input id="invite_fullname" name="fullname" type="text" value="{{ old('fullname') }}" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_username" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Username</label>
                            <input id="invite_username" name="username" type="text" value="{{ old('username') }}" required class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_email" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</label>
                            <input id="invite_email" name="email" type="email" value="{{ old('email') }}" required class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_phone" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Phone</label>
                            <input id="invite_phone" name="phone_number" type="text" value="{{ old('phone_number') }}" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_password" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Temp password</label>
                            <input id="invite_password" name="password" type="password" required class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="invite_password_confirmation" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Confirm</label>
                            <input id="invite_password_confirmation" name="password_confirmation" type="password" required class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                            <i class="ri-user-add-line text-sm"></i>
                            Add administrator
                        </button>
                    </div>
                </form>

                @if ($others->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Existing admins</h3>
                            <span class="rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-xs font-bold text-[#0b3019]">{{ $others->count() }}</span>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($others as $other)
                                <li class="py-2.5 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $other->fullname ?? $other->username }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $other->email }}</p>
                                    </div>
                                    <p class="text-xs text-slate-400 shrink-0">{{ optional($other->created_at)->diffForHumans() }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </article>
        </section>

        <!-- System snapshots -->
        <section class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-400">
            <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0b3019]/8 text-[#0b3019]">
                        <i class="ri-database-2-line text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">System snapshots</h2>
                        <p class="text-xs text-slate-400">Capture configuration or database fingerprints for backups and audits.</p>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-slate-100">
                <form method="POST" action="{{ route('admin.profile.snapshots.store') }}" class="grid gap-3 md:grid-cols-[1fr_180px_auto] items-end">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label for="snapshot_notes" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Snapshot notes</label>
                        <input id="snapshot_notes" name="notes" type="text" value="{{ old('notes') }}" placeholder="Optional context (e.g. before deployment)" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="snapshot_type" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Type</label>
                        <div class="relative">
                            <select id="snapshot_type" name="type" class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white pl-3 pr-8 text-xs text-slate-700 transition focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019]">
                                <option value="system" @selected(old('type') === 'system')>System overview</option>
                                <option value="database" @selected(old('type') === 'database')>Database structure</option>
                            </select>
                            <i class="ri-arrow-down-s-line pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>
                    <button type="submit" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
                        <i class="ri-database-2-line text-sm"></i>
                        Generate
                    </button>
                </form>
            </div>

            {{-- Snapshots table --}}
            @if (empty($snapshots))
                <div class="px-5 py-10 text-center">
                    <span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
                        <i class="ri-database-2-line text-2xl text-slate-300"></i>
                    </span>
                    <p class="mt-3 text-sm font-semibold text-slate-500">No snapshots yet</p>
                    <p class="text-xs text-slate-400 mt-1">Use the form above to generate your first snapshot.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left">File</th>
                                <th scope="col" class="px-5 py-3 text-left">Type</th>
                                <th scope="col" class="px-5 py-3 text-left">Generated</th>
                                <th scope="col" class="px-5 py-3 text-left">Notes</th>
                                <th scope="col" class="px-5 py-3 text-left">Size</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($snapshots as $snapshot)
                                <tr class="hover:bg-slate-50/60 transition">
                                    @php($type = $snapshot['content']['type'] ?? 'system')
                                    @php($isDatabase = $type === 'database')
                                    @php($tables = $snapshot['content']['snapshot']['tables'] ?? [])
                                    @php($tableCount = is_array($tables) ? count($tables) : 0)
                                    @php($extension = strtoupper(pathinfo($snapshot['filename'], PATHINFO_EXTENSION)))
                                    @php($bytes = $snapshot['size'] ?? 0)
                                    @php($sizeLabel = $bytes >= 1048576 ? number_format($bytes / 1048576, 2) . ' MB' : number_format($bytes / 1024, 2) . ' KB')
                                    <td class="px-5 py-3.5 text-xs font-mono text-slate-700">{{ $snapshot['filename'] }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-[10px] font-bold text-[#0b3019]">
                                            <i class="{{ $isDatabase ? 'ri-database-line' : 'ri-shield-check-line' }}"></i>
                                            {{ $isDatabase ? 'Database' : 'System' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500">{{ optional($snapshot['last_modified'])->diffForHumans() }}</td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500">
                                        <p>{{ $snapshot['content']['notes'] ?? ($isDatabase ? 'Full database export' : 'System overview') }}</p>
                                        @if ($isDatabase)
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $tableCount }} tables</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 tabular-nums">{{ $sizeLabel }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('admin.profile.snapshots.download', base64_encode($snapshot['path'])) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]" download>
                                            <i class="ri-download-2-line"></i>
                                            Download {{ $extension }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>
</x-layouts.admin>
