@php($title = 'System Maintenance')

<x-layouts.admin :title="$title">
    <div class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
        <div class="space-y-6">

            <!-- Header -->
            <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-semibold text-rose-400">
                        <i class="ri-alert-line text-sm"></i>
                        <span>Hazard zone</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">System maintenance</h1>
                    <p class="text-sm text-slate-500">Advanced tools to fix database inconsistencies, sync records, and optimize performance. Use with caution.</p>
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

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 animate-fade-slide">
                    <div class="flex items-center gap-2">
                        <i class="ri-error-warning-line text-base text-rose-500"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Quick action cards -->
            <div class="grid gap-4 md:grid-cols-2 animate-fade-slide animate-fade-slide-delay-200">

                <!-- Dummy Accounts -->
                <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800">3. Dummy accounts</h3>
                    <p class="mt-1 text-xs text-slate-500">Unverified students with non-school emails.</p>
                    <div class="mt-4 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Detected</span>
                        <span class="text-lg font-bold text-indigo-600 tabular-nums">{{ count($potentialDummies) }}</span>
                    </div>
                    <a href="#dummy-review" class="mt-3 h-9 flex items-center justify-center w-full rounded-lg bg-[#0b3019] text-xs font-semibold text-white transition hover:bg-[#072412] active:scale-95">
                        Review list
                    </a>
                </article>

                <!-- Optimization -->
                <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800">4. Optimization</h3>
                    <p class="mt-1 text-xs text-slate-500">Clear all caches to ensure logic and style fresh sync.</p>
                    <form action="{{ route('admin.maintenance.optimize') }}" method="POST" class="mt-10">
                        @csrf
                        <button type="submit" class="h-9 w-full rounded-lg bg-[#0b3019] text-xs font-semibold text-white transition hover:bg-[#072412] active:scale-95">
                            Full sync
                        </button>
                    </form>
                </article>
            </div>

            <!-- Suspicious Account Review -->
            @if(count($potentialDummies) > 0)
            <section id="dummy-review" class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200" x-data="{ selected: [] }">
                <div class="border-b border-slate-100 bg-slate-50/40 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Suspicious account review</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Accounts using non-standard emails — excludes @st.umat, @umat, @gmail, @outlook, @yahoo, and @icloud.</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form action="{{ route('admin.maintenance.delete-dummies') }}" method="POST" onsubmit="return confirm('Delete all detected accounts and related records?')">
                                @csrf
                                <button type="submit" class="h-8 inline-flex items-center gap-1.5 rounded-lg border border-rose-200 px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 active:scale-95">
                                    Wipe everything
                                </button>
                            </form>
                            <form action="{{ route('admin.maintenance.delete-dummies') }}" method="POST" x-show="selected.length > 0" onsubmit="return confirm('Delete ' + selected.length + ' selected accounts and related records?')">
                                @csrf
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 text-xs font-semibold text-white transition hover:bg-indigo-700 active:scale-95">
                                    Delete selected (<span x-text="selected.length"></span>)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50/80 text-[10px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                            <tr>
                                <th class="px-5 py-3 w-10">
                                    <input type="checkbox" @change="if($el.checked) { selected = [ @foreach($potentialDummies as $d) '{{ $d->user_id }}', @endforeach ] } else { selected = [] }" class="rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]">
                                </th>
                                <th class="px-5 py-3">Student</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($potentialDummies as $dummy)
                            <tr class="transition hover:bg-slate-50/60" :class="selected.includes('{{ $dummy->user_id }}') ? 'bg-indigo-50/20' : ''">
                                <td class="px-5 py-3.5">
                                    <input type="checkbox" :value="'{{ $dummy->user_id }}'" x-model="selected" class="rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]">
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-bold text-slate-900">{{ $dummy->fullname }}</p>
                                    <p class="text-[10px] text-slate-400">@{{ $dummy->username }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 max-w-[180px] truncate">{{ $dummy->email }}</td>
                                <td class="px-5 py-3.5">
                                    @if($dummy->email_verified_at)
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Verified</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form action="{{ route('admin.maintenance.delete-dummies') }}" method="POST" class="inline" onsubmit="return confirm('Delete this account and related records?')">
                                        @csrf
                                        <input type="hidden" name="ids[]" value="{{ $dummy->user_id }}">
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            <!-- Database Migrations -->
            <article class="rounded-2xl border border-amber-100 bg-amber-50/30 p-5 shadow-sm animate-fade-slide animate-fade-slide-delay-200">
                <div class="flex items-center gap-3 mb-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 text-sm font-bold">8</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Database storage &amp; schema</h3>
                        <p class="text-xs text-slate-500">Run safe database migrations to update the system schema or apply structural changes.</p>
                    </div>
                </div>
                <form action="{{ route('admin.maintenance.migrate') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" onclick="return confirm('Ensure you have a database backup before running migrations. Proceed?')" class="h-9 inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 text-xs font-semibold text-white transition hover:bg-amber-700 active:scale-95">
                        <i class="ri-database-2-line text-sm"></i>
                        Run database migrations
                    </button>
                    <p class="mt-2 text-[10px] text-slate-400 flex items-center gap-1.5">
                        <i class="ri-information-line text-amber-500"></i>
                        This will run <code class="font-mono">php artisan migrate --force</code> on the server.
                    </p>
                </form>
            </article>

        </div>
    </div>
</x-layouts.admin>
