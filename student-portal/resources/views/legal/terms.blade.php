@php($title = 'Terms & Conditions')

<x-layouts.marketing :title="$title">
    <section class="mx-auto w-full max-w-[1200px] space-y-8 px-6 py-16 sm:py-24">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#0b3019]"><i class="ri-arrow-left-line"></i> Back</a>

        <header class="space-y-3 text-center">
            <h1 class="text-3xl font-semibold text-[#0b3019] sm:text-4xl">Terms &amp; Conditions</h1>
            <p class="text-sm text-slate-500">Effective {{ now()->isoFormat('MMMM D, YYYY') }}</p>
        </header>

        <div class="space-y-6 rounded-[24px] border border-slate-100 bg-white/95 p-10 shadow-lg shadow-slate-200/40">
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">1. Acceptance of terms</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    By accessing or using the ACSES Student Portal you agree to be bound by these terms and any referenced policies. If you do not agree, please discontinue use of the portal.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">2. Student responsibilities</h2>
                <ul class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-600">
                    <li>Provide accurate information during registration and keep your profile up to date.</li>
                    <li>Maintain the confidentiality of your login credentials and promptly report suspected breaches.</li>
                    <li>Use the portal solely for academic and administrative purposes related to the ACSES program.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">3. Acceptable use</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    You agree not to misuse the platform, including but not limited to attempting unauthorized access, distributing malware, engaging in harassment, or violating intellectual-property rights in materials shared through the portal.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">4. Content ownership</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Course materials, announcements, and resources provided via the portal remain the property of their respective authors or the institution. Redistribution is permitted only with explicit authorization.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">5. Service availability</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We strive for high availability but do not guarantee uninterrupted access. Scheduled maintenance or technical issues may temporarily limit functionality. Critical updates will be communicated in advance whenever possible.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">6. Changes to these terms</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We may update these terms from time to time. Material changes will be highlighted on the portal. Continued use after an update constitutes acceptance of the revised terms.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">7. Contact</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Questions about these terms can be directed to <a href="mailto:acsesrepos@gmail.com" class="font-semibold text-[#0b3019] hover:underline">acsesrepos@gmail.com</a>.
                </p>
            </section>
        </div>
    </section>
</x-layouts.marketing>
