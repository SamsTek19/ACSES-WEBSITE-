<x-layouts.dashboard title="ACSES Developers">
    @push('styles')
        <style>
            .grid-mesh {
                background-size: 32px 32px;
                background-image: 
                    linear-gradient(to right, rgba(11, 48, 25, 0.03) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(11, 48, 25, 0.03) 1px, transparent 1px);
            }
        </style>
    @endpush

    @php
        $hasContactRoute = \Illuminate\Support\Facades\Route::has('marketing.contact');
    @endphp

    <div class="relative bg-slate-50/60 min-h-screen pb-20">
        <!-- Minimal Grid Mesh Background -->
        <div class="absolute inset-0 grid-mesh pointer-events-none"></div>

        <div class="relative mx-auto w-full max-w-[1400px] px-5 py-12 sm:px-6 lg:px-8">
            <div class="space-y-16">

                <!-- ================= HERO HEADER ================= -->
                <header class="mx-auto max-w-3xl text-center space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-md bg-[#0b3019]/5 border border-[#0b3019]/15 px-3 py-1 text-xs font-semibold text-[#0b3019]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#0b3019]"></span>
                        ACSES Product Studio
                    </span>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                        Meet the Engineers Behind ACSES
                    </h1>
                    <p class="mx-auto max-w-xl text-sm text-slate-500 leading-relaxed sm:text-base">
                        We design, build, and maintain the digital infrastructure driving student operations, payments, and portal workflows across the department.
                    </p>
                </header>

                <!-- ================= CORE DEVELOPER TEAM GRID ================= -->
                <div class="space-y-8">
                    <div class="text-center space-y-1">
                        <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">The Engineering Team</h2>
                        <p class="text-xs text-slate-500">The core architects behind portal delivery, security, and interface design.</p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        
                        <!-- Kingsley Adu -->
                        <article class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#0b3019]/30 hover:shadow-md">
                            <!-- Image Container -->
                            <div class="relative h-64 w-full overflow-hidden rounded-lg bg-slate-100">
                                <img src="{{ asset('assets/images/Kingsley.jpg') }}" alt="Kingsley Adu" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            </div>

                            <div class="flex flex-1 flex-col justify-between pt-5 space-y-4">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-bold text-slate-900">Kingsley Adu</h3>
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Full-stack</span>
                                    </div>
                                    <p class="text-xs font-semibold text-[#0b3019] mt-0.5">Project Lead</p>
                                    <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                        Architects the end-to-end portal schema, building rigid auth policies, secure middle-tier guards, and coordinating general delivery.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-slate-100">
                                    <a href="https://www.linkedin.com/in/kingsley-aduhene-778538224/" target="_blank" rel="noopener" class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-98">
                                        <i class="ri-linkedin-box-fill text-sm text-[#0b3019]"></i>
                                        <span>Connect on LinkedIn</span>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <!-- Alfred Boakye -->
                        <article class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#0b3019]/30 hover:shadow-md">
                            <!-- Image Container -->
                            <div class="relative h-64 w-full overflow-hidden rounded-lg bg-slate-100">
                                <img src="{{ asset('assets/images/Alfred.jpg') }}" alt="Alfred Boakye" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            </div>

                            <div class="flex flex-1 flex-col justify-between pt-5 space-y-4">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-bold text-slate-900">Alfred Boakye</h3>
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Backend</span>
                                    </div>
                                    <p class="text-xs font-semibold text-[#0b3019] mt-0.5">Lead Portal Engineer</p>
                                    <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                        Translates complex student workflows into robust relational models and controllers, backing operations with strict caching engines.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-slate-100">
                                    <a href="https://www.linkedin.com/in/alfredboakye/" target="_blank" rel="noopener" class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-98">
                                        <i class="ri-linkedin-box-fill text-sm text-[#0b3019]"></i>
                                        <span>Connect on LinkedIn</span>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <!-- Obed Acquah -->
                        <article class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#0b3019]/30 hover:shadow-md">
                            <!-- Image Container -->
                            <div class="relative h-64 w-full overflow-hidden rounded-lg bg-slate-100">
                                <img src="{{ asset('assets/images/Obed.jpeg') }}" alt="Obed Acquah" class="h-full w-full object-cover object-top transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            </div>

                            <div class="flex flex-1 flex-col justify-between pt-5 space-y-4">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-bold text-slate-900">Obed Acquah</h3>
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Frontend</span>
                                    </div>
                                    <p class="text-xs font-semibold text-[#0b3019] mt-0.5">Web Experience Engineer</p>
                                    <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                        Crafts responsive client layers, styling consistent and fast components that deliver smooth interactive experiences.
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-slate-100">
                                    <a href="https://www.linkedin.com/in/obed-acquah-017687301/" target="_blank" rel="noopener" class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-98">
                                        <i class="ri-linkedin-box-fill text-sm text-[#0b3019]"></i>
                                        <span>Connect on LinkedIn</span>
                                    </a>
                                </div>
                            </div>
                        </article>

                    </div>
                </div>

                <!-- ================= CALL TO ACTION ================= -->
                <div 
                    x-data="{ 
                        email: '', 
                        submitted: false, 
                        loading: false, 
                        submitForm() {
                            if (!this.email) return;
                            this.loading = true;
                            setTimeout(() => {
                                this.loading = false;
                                this.submitted = true;
                                this.email = '';
                            }, 800);
                        }
                    }"
                    class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm max-w-3xl mx-auto space-y-6"
                >
                    <div class="space-y-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                            Engineering Support
                        </span>
                        <h2 class="text-2xl font-bold text-slate-900">Need help shaping portal tools?</h2>
                        <p class="mx-auto max-w-lg text-xs text-slate-500 leading-relaxed">
                            Our team collaborates with departmental leadership and administrative heads to maintain resilient student software tools.
                        </p>
                    </div>
                    
                    <div class="max-w-md mx-auto">
                        <form @submit.preventDefault="submitForm" class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input 
                                    type="email" 
                                    x-model="email" 
                                    placeholder="Enter administrative email" 
                                    required 
                                    :disabled="submitted || loading"
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#0b3019] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0b3019] transition"
                                />
                            </div>
                            <button 
                                type="submit" 
                                :disabled="submitted || loading || !email"
                                class="h-9 rounded-lg px-4 text-xs font-semibold text-white bg-[#0b3019] hover:bg-[#0b3019]/90 transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                            >
                                <span x-show="loading" class="animate-spin h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                                <span x-show="!loading" x-text="submitted ? 'Submitted' : 'Get in Touch'">Get in Touch</span>
                            </button>
                        </form>
                        
                        <div 
                            x-show="submitted" 
                            x-transition
                            class="mt-3 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2 justify-center"
                        >
                            <i class="ri-checkbox-circle-line text-emerald-600 text-base"></i>
                            <span>Inquiry logged! We will reach out shortly.</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-wrap justify-center gap-4 text-xs text-slate-500">
                        <a href="mailto:hello@acses.edu" class="inline-flex items-center gap-1.5 hover:text-[#0b3019] transition">
                            <i class="ri-mail-line text-sm"></i>
                            <span>hello@acses.edu</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.dashboard>
