@php($title = 'Accessibility Statement')

<x-layouts.marketing :title="$title">
    <section class="mx-auto w-full max-w-[1200px] space-y-8 px-6 py-16 sm:py-24">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#0b3019]"><i class="ri-arrow-left-line"></i> Back</a>

        <header class="space-y-3 text-center">
            <h1 class="text-3xl font-semibold text-[#0b3019] sm:text-4xl">Accessibility Statement</h1>
            <p class="text-sm text-slate-500">Last updated {{ now()->isoFormat('MMMM D, YYYY') }}</p>
        </header>

        <div class="space-y-6 rounded-[24px] border border-slate-100 bg-white/95 p-10 shadow-lg shadow-slate-200/40">
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Our commitment</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    The ACSES team is committed to providing a portal that is accessible to all students, including those using assistive technologies. We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Measures we take</h2>
                <ul class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-600">
                    <li>Use semantic HTML and consistent heading structures.</li>
                    <li>Ensure sufficient color contrast and keyboard navigability.</li>
                    <li>Provide descriptive labels, alt text, and focus indicators.</li>
                    <li>Regularly test forms and new features with accessibility tools.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Ongoing improvements</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    Accessibility is an ongoing effort. When issues are identified, we prioritize fixes as part of our development cycle and monitor new features for compliance.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Feedback</h2>
                <p class="text-sm leading-relaxed text-slate-600">
                    If you encounter accessibility barriers, please let us know at <a href="mailto:acsesrepos@gmail.com" class="font-semibold text-[#0b3019] hover:underline">acsesrepos@gmail.com</a>. Include details about the issue and the technology you are using so we can respond effectively.
                </p>
            </section>
        </div>
    </section>
</x-layouts.marketing>
