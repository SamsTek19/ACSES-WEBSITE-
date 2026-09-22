@php($title = $title ?? 'Manage academic resources')

<x-layouts.admin :title="$title">
	<div class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
		<div class="space-y-6">

			<header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between animate-fade-slide">
				<div class="space-y-1">
					<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
						<i class="ri-book-3-line text-sm" aria-hidden="true"></i>
						<span>Academic resources</span>
					</div>
					<h1 class="text-2xl font-bold tracking-tight text-slate-900">Resource library</h1>
					<p class="text-sm text-slate-500">Upload lecture materials, past questions, and helpful links with clear targeting by class and year.</p>
				</div>
				<div class="flex flex-wrap items-center gap-2 shrink-0">
					<a href="{{ route('admin.resources.create') }}" class="h-9 flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95" aria-label="Create new academic resource">
						<i class="ri-add-line text-sm" aria-hidden="true"></i>
						New resource
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
						Showing {{ $resources->firstItem() ?? 0 }}–{{ $resources->lastItem() ?? 0 }} of {{ $resources->total() }} resources
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
								<th scope="col" class="px-6 py-3">Resource</th>
								<th scope="col" class="px-6 py-3">Type</th>
								<th scope="col" class="px-6 py-3">Audience</th>
								<th scope="col" class="px-6 py-3">Visibility</th>
								<th scope="col" class="px-6 py-3 text-right">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100 bg-white">
							@forelse ($resources as $resource)
								<tr class="transition hover:bg-slate-50/60">
									<td class="px-6 py-3.5">
										<div class="space-y-1 max-w-xs">
											<p class="text-sm font-semibold text-slate-900 leading-tight">{{ $resource->title }}</p>
											<p class="text-xs text-slate-400 line-clamp-2">{{ Str::limit($resource->description, 80) }}</p>
											@if ($resource->category)
												<span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-[10px] font-semibold text-[#0b3019]/80">
													<i class="ri-bookmark-line text-xs"></i>
													{{ Str::headline($resource->category) }}
												</span>
											@endif
										</div>
									</td>
									<td class="px-6 py-3.5">
										<span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
											<i class="ri-{{ $resource->resource_type === 'file' ? 'file-line' : 'external-link-line' }}"></i>
											{{ ucfirst($resource->resource_type) }}
										</span>
									</td>
									<td class="px-6 py-3.5">
										@php
											$classes = $resource->target_classes ?? [];
											$years   = $resource->target_years ?? [];
										@endphp
										<div class="flex flex-wrap items-center gap-1.5">
											@if (empty($classes) && empty($years))
												<span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">All students</span>
											@endif
											@foreach ($classes as $class)
												<span class="inline-flex items-center gap-1 rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-xs font-semibold text-[#0b3019]">{{ $class }}</span>
											@endforeach
											@foreach ($years as $year)
												<span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Yr {{ $year }}</span>
											@endforeach
										</div>
									</td>
									<td class="px-6 py-3.5">
										<span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold {{ $resource->visibility === 'student' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
											<i class="ri-eye-{{ $resource->visibility === 'student' ? '2-line' : 'close-line' }}"></i>
											{{ $resource->visibility === 'student' ? 'Visible' : 'Hidden' }}
										</span>
									</td>
									<td class="px-6 py-3.5">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('admin.resources.edit', $resource) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
												<i class="ri-edit-line" aria-hidden="true"></i>
												Edit
											</a>
											<form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" class="inline-flex" onsubmit="return confirm('Delete this resource?');">
												@csrf
												@method('DELETE')
												<button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
													<i class="ri-delete-bin-line" aria-hidden="true"></i>
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
												<i class="ri-book-mark-line text-2xl text-slate-300"></i>
											</span>
											<p class="text-sm font-semibold text-slate-600">No resources yet</p>
											<p class="text-xs text-slate-400">Upload your first resource to make materials available to students.</p>
											<a href="{{ route('admin.resources.create') }}" class="h-8 inline-flex items-center gap-1.5 rounded-lg bg-[#0b3019] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#072412] active:scale-95">
												<i class="ri-add-line text-sm"></i>
												Upload resource
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
					@forelse ($resources as $resource)
						<article class="p-4">
							<div class="flex items-start justify-between gap-3">
								<div class="min-w-0 flex-1">
									<p class="text-sm font-semibold text-slate-900 leading-tight">{{ $resource->title }}</p>
									<p class="mt-0.5 text-xs text-slate-400 line-clamp-2">{{ Str::limit($resource->description, 100) }}</p>
								</div>
								<span class="shrink-0 inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $resource->visibility === 'student' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
									<i class="ri-eye-{{ $resource->visibility === 'student' ? '2-line' : 'close-line' }}"></i>
									{{ $resource->visibility === 'student' ? 'Visible' : 'Hidden' }}
								</span>
							</div>
							@php
								$classes = $resource->target_classes ?? [];
								$years   = $resource->target_years ?? [];
							@endphp
							<div class="mt-2 flex flex-wrap gap-1.5">
								<span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
									<i class="ri-{{ $resource->resource_type === 'file' ? 'file-line' : 'external-link-line' }}"></i>
									{{ ucfirst($resource->resource_type) }}
								</span>
								@if (empty($classes) && empty($years))
									<span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">All students</span>
								@endif
								@foreach ($classes as $class)
									<span class="inline-flex items-center rounded-md bg-[#0b3019]/8 px-2 py-0.5 text-xs font-semibold text-[#0b3019]">{{ $class }}</span>
								@endforeach
								@foreach ($years as $year)
									<span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Yr {{ $year }}</span>
								@endforeach
							</div>
							<div class="mt-3 flex items-center gap-2">
								<a href="{{ route('admin.resources.edit', $resource) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#0b3019]/30 hover:text-[#0b3019]">
									<i class="ri-edit-line" aria-hidden="true"></i>
									Edit
								</a>
								<form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" class="inline-flex" onsubmit="return confirm('Delete this resource?');">
									@csrf
									@method('DELETE')
									<button type="submit" class="inline-flex items-center gap-1 rounded-md border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
										<i class="ri-delete-bin-line" aria-hidden="true"></i>
										Delete
									</button>
								</form>
							</div>
						</article>
					@empty
						<div class="p-10 text-center">
							<span class="flex h-12 w-12 mx-auto items-center justify-center rounded-full bg-slate-50">
								<i class="ri-book-mark-line text-2xl text-slate-300"></i>
							</span>
							<p class="mt-3 text-sm font-semibold text-slate-600">No resources yet</p>
						</div>
					@endforelse
				</div>

				{{-- Pagination --}}
				<div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-3 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
					<p class="text-xs text-slate-400">Page {{ $resources->currentPage() }} of {{ $resources->lastPage() }}</p>
					<div class="sm:ml-auto flex justify-center sm:justify-end">
						{{ $resources->appends(['per_page' => $currentPerPage])->onEachSide(1)->links() }}
					</div>
				</div>
			</section>
		</div>
	</div>
</x-layouts.admin>
