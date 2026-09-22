<footer class="mt-12 border-t border-slate-200 bg-white">
    <div class="mx-auto w-full max-w-[1600px] px-5 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row text-xs text-slate-500">
            
            <!-- Left: Branding & Copyright -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="ACSES Logo" class="h-6 w-6 rounded object-contain" loading="lazy">
                <span class="font-bold text-slate-800">ACSES Student Portal</span>
                <span class="h-3 w-px bg-slate-200"></span>
                <span>&copy; {{ now()->year }} ACSES. All rights reserved.</span>
            </div>

            <!-- Right: Developers Menu & Legals -->
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                <a href="{{ route('marketing.developers') }}" class="inline-flex items-center gap-1.5 font-semibold text-[#0b3019] hover:underline">
                    <i data-lucide="users" class="h-3.5 w-3.5"></i>
                    <span>Developers</span>
                </a>
                <span class="h-3 w-px bg-slate-200"></span>
                <a href="{{ route('legal.privacy') }}" class="hover:text-slate-900 transition-colors">Privacy Policy</a>
                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                <a href="{{ route('legal.terms') }}" class="hover:text-slate-900 transition-colors">Terms of Service</a>
                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                <a href="{{ route('legal.cookies') }}" class="hover:text-slate-900 transition-colors">Cookie Policy</a>
            </div>

        </div>
    </div>
</footer>
