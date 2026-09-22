@php($title = 'Cookie Policy')

<x-layouts.marketing :title="$title">
    <section class="mx-auto w-full max-w-[1200px] space-y-8 px-6 py-16 sm:py-24">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#0b3019]"><i class="ri-arrow-left-line"></i> Back</a>

        <header class="space-y-3 text-center">
            <h1 class="text-3xl font-semibold text-[#0b3019] sm:text-4xl">Cookie Policy</h1>
            <p class="text-sm text-slate-500">Effective {{ now()->isoFormat('MMMM D, YYYY') }}</p>
        </header>

        <div class="space-y-6 rounded-[24px] border border-slate-100 bg-white/95 p-10 shadow-lg shadow-slate-200/40">
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">1. What are cookies?</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Cookies are small data files placed on your device when you browse our portal. They help us remember your preferences, keep sessions secure, and understand how the portal is used.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">2. How we use cookies</h2>
                <ul class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-600">
                    <li><strong>Essential cookies</strong> keep you signed in and maintain security protections.</li>
                    <li><strong>Preference cookies</strong> store interface choices such as language or notification settings.</li>
                    <li><strong>Analytics cookies</strong> help us measure usage so we can improve reliability and performance.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">3. Managing cookies</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Most browsers allow you to manage or disable cookies through their settings. Blocking essential cookies may limit access to parts of the portal. Analytics cookies can be opted out of through your browser preferences.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">4. Third-party services</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We may use trusted analytics or communication providers that place their own cookies. We require those partners to comply with data-protection standards and only use cookies for the services we request.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">5. Updates</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We may update this policy to reflect new features or regulatory guidance. Check back periodically for the latest version.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">6. Contact</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    If you have questions about our cookie practices, contact <a href="mailto:acsesrepos@gmail.com" class="font-semibold text-[#0b3019] hover:underline">acsesrepos@gmail.com</a>.
                </p>
            </section>
        </div>
    </section>
</x-layouts.marketing>
