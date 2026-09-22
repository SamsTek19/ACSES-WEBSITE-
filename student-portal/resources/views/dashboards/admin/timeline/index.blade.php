@php($title = $title ?? 'Academic timeline')

<x-layouts.admin :title="$title">
	<div class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
		<div class="space-y-6">

			<header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
				<div class="space-y-1">
					<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
						<i class="ri-time-line text-sm" aria-hidden="true"></i>
						<span>Academic timeline</span>
					</div>
					<h1 class="text-2xl font-bold tracking-tight text-slate-900">Milestones &amp; checkpoints</h1>
					<p class="text-sm text-slate-500">Publish semester checkpoints so students stay ahead of registrations, exams, and breaks.</p>
				</div>
				<div class="flex flex-wrap items-center gap-2 shrink-0">
					<a href="{{ route('admin.timeline.create') }}" class="h-9 flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95" aria-label="Create timeline entry">
						<i class="ri-add-line text-sm" aria-hidden="true"></i>
						New entry
					</a>
				</div>
			</header>

			@if (session('status'))
				<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-slide">
					<div class="flex items-center gap-2">
						<i class="ri-check-double-line text-base text-emerald-600" aria-hidden="true"></i>
						<p>{{ session('status') }}</p>
					</div>
				</div>
			@endif

			<section class="space-y-0 rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden animate-fade-slide animate-fade-slide-delay-200">

				{{-- Count + rows-per-page bar --}}
				<div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/40 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
					<p class="text-xs font-semibold text-slate-600">
						Showing {{ $entries->firstItem() ?? 0 }}–{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
					</p>
					<form method="GET" class="flex items-center justify-center gap-2 sm:justify-end" x-data>
						@foreach (request()->except(['per_page', 'page']) as $key => $value)
							<input type="hidden" name="{{ $key }}" value="{{ $value }}">
						@endforeach
						<label for="per_page" class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rows</label>
						<select id="per_page" name="per_page" class="h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700 focus:border-[#0b3019] focus:ring-1 focus:ring-[#0b3019]" x-on:change="$el.form.submit()">
							@foreach ($perPageOptions as $option)
								<option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
							@endforeach
						</select>
					</form>
				</div>

				{{-- Desktop table --}}
				<div class="hidden md:block">
					<table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
						<thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">
							<tr>
								<th scope="col" class="px-6 py-3">Milestone</th>
								<th scope="col" class="px-6 py-3">Date</th>
								<th scope="col" class="px-6 py-3">Academic year</th>
								<th scope="col" class="px-6 py-3">Status</th>
								<th scope="col" class="px-6 py-3 text-right"><span class="sr-only">Actions</span></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100 bg-white">
							@forelse ($entries as $entry)
								<tr class="transition hover:bg-slate-50/60">
									<td class="px-6 py-3.5">
										<p class="text-sm font-semibold text-slate-900 leading-tight">{{ $entry->title }}</p>
									</td>
									<td class="px-6 py-3.5 text-xs text-slate-500 tabular-nums">
										{{ optional($entry->starts_at)->format('M j, Y') ?? '—' }}
									</td>
									<td class="px-6 py-3.5 text-xs text-slate-500">{{ $entry->academic_year ?? '—' }}</td>
									<td class="px-6 py-3.5">
										@if ($entry->is_published)
											<span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
												<span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
												Live
											</span>
										@else
											<span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">
												<span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
												Hidden
											</span>
										@endif
									</td>
									<td class="px-6 py-3.5">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('admin.timeline.edit', $entry) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
												<i class="ri-edit-line" aria-hidden="true"></i>
												Edit
											</a>
											<form method="POST" action="{{ route('admin.timeline.destroy', $entry) }}" onsubmit="return confirm('Delete this timeline entry?');">
												@csrf
												@method('DELETE')
												<button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
													<i class="ri-delete-bin-6-line" aria-hidden="true"></i>
													Delete
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-6 py-12 text-center">
										<div class="flex flex-col items-center gap-3">
											<span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
												<i class="ri-time-line text-2xl text-slate-300"></i>
											</span>
											<p class="text-sm font-semibold text-slate-600">No milestones yet</p>
											<p class="text-xs text-slate-400">Create your first milestone to guide students through the semester.</p>
											<a href="{{ route('admin.timeline.create') }}" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
												<i class="ri-add-line text-sm"></i>
												Create milestone
											</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				{{-- Mobile cards --}}
				<div class="divide-y divide-slate-100 md:hidden">
					@forelse ($entries as $entry)
						<article class="p-4">
							<div class="flex items-start justify-between gap-3">
								<p class="text-sm font-semibold text-slate-900 leading-tight">{{ $entry->title }}</p>
								@if ($entry->is_published)
									<span class="shrink-0 inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
										<span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
										Live
									</span>
								@else
									<span class="shrink-0 inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">
										<span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
										Hidden
									</span>
								@endif
							</div>
							<dl class="mt-2 grid grid-cols-2 gap-y-1.5 text-xs text-slate-500">
								<div><dt class="text-slate-400">Date</dt><dd class="font-medium tabular-nums">{{ optional($entry->starts_at)->format('M j, Y') ?? '—' }}</dd></div>
								<div><dt class="text-slate-400">Year</dt><dd class="font-medium">{{ $entry->academic_year ?? '—' }}</dd></div>
							</dl>
							<div class="mt-3 flex items-center gap-2">
								<a href="{{ route('admin.timeline.edit', $entry) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
									<i class="ri-edit-line" aria-hidden="true"></i>
									Edit
								</a>
								<form method="POST" action="{{ route('admin.timeline.destroy', $entry) }}" class="inline" onsubmit="return confirm('Delete this timeline entry?');">
									@csrf
									@method('DELETE')
									<button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
										<i class="ri-delete-bin-6-line" aria-hidden="true"></i>
										Delete
									</button>
								</form>
							</div>
						</article>
					@empty
						<div class="p-10 text-center">
							<span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
								<i class="ri-time-line text-2xl text-slate-300"></i>
							</span>
							<p class="mt-3 text-sm font-semibold text-slate-600">No milestones yet</p>
						</div>
					@endforelse
				</div>

				{{-- Pagination --}}
				<div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
					<p class="text-xs text-slate-400">Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}</p>
					<div class="sm:ml-auto flex justify-center sm:justify-end">
						{{ $entries->links('vendor.pagination.data-limit') }}
					</div>
				</div>
			</section>
		</div>
	</div>
</x-layouts.admin>
