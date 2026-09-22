@php($title = 'Privacy Policy')

<x-layouts.marketing :title="$title">
    <section class="mx-auto w-full max-w-[1200px] space-y-8 px-6 py-16 sm:py-24">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#0b3019]"><i class="ri-arrow-left-line"></i> Back</a>

        <header class="space-y-3 text-center">
            <h1 class="text-3xl font-semibold text-[#0b3019] sm:text-4xl">Privacy Policy</h1>
            <p class="text-sm text-slate-500">Effective {{ now()->isoFormat('MMMM D, YYYY') }}</p>
        </header>

        <div class="space-y-6 rounded-[24px] border border-slate-100 bg-white/95 p-10 shadow-lg shadow-slate-200/40">
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">1. Information we collect</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We collect the profile and academic data you provide during registration, along with usage logs that help us maintain security and improve the portal. Sensitive records such as grades remain in institutional systems and are not stored here.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">2. How we use your data</h2>
                <ul class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-600">
                    <li>Authenticate you and deliver core functionality such as course registration and dues management.</li>
                    <li>Send notifications about timetable changes, academic resources, and important announcements.</li>
                    <li>Monitor system health, detect abuse, and compile high-level analytics for service planning.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">3. Sharing and disclosure</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We do not sell or rent student information. Data may be shared with authorized ACSES staff or service providers who support the portal under confidentiality agreements, or when required by law.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">4. Data retention</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Account records are retained while your ACSES profile is active. Backups are purged on a rolling basis. You can request removal of optional data by contacting the academic office at <a href="mailto:acsesrepos@gmail.com" class="font-semibold text-[#0b3019] hover:underline">acsesrepos@gmail.com</a>.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">5. Your choices</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    You may review or update your profile from the dashboard. Opt out of optional emails via the notification settings section. To deactivate your account, contact the program coordinator.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">6. Security</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We employ industry-standard safeguards including encryption in transit, role-based access controls, and regular security reviews. Despite these efforts, no system is completely secure; please report suspected issues immediately.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">7. Updates to this policy</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    We may revise this policy to reflect new services or regulatory changes. Significant updates will be highlighted on the portal. Continued use after publication indicates acceptance of the revised policy.
                </p>
            </section>
        </div>
    </section>
</x-layouts.marketing>
