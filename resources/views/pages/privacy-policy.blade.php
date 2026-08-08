<x-layouts.app title="Privacy Policy"
    description="How Litter Free Leeds handles the information you share with us.">

    <x-page-hero eyebrow="Legal" heading="Privacy Policy"
        subheading="How we handle the information you share with us." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                <div class="lg:col-span-4">
                    <div
                        class="border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-500/10 p-6 flex gap-3">
                        <x-heroicon-o-exclamation-triangle
                            class="w-6 h-6 shrink-0 text-amber-700 dark:text-amber-300" />
                        <p class="text-sm text-amber-900 dark:text-amber-200 leading-relaxed">
                            <strong class="font-bold">Placeholder.</strong> This page still needs its real policy text
                            before the site goes live.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-8 max-w-3xl divide-y divide-ink-950/12 dark:divide-white/12">
                    @foreach ([
        'What we collect' => 'When you create a volunteer account we store the details you give us so we can put you in touch with a group near you and record the bags you return.',
        'How we use it' => 'Only to run Litter Free Leeds — coordinating picks, providing equipment, and reporting on the overall impact of the group. We do not sell your details.',
    ] as $heading => $body)
                        <div class="py-8 first:pt-0">
                            <h2 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-4">
                                {{ $heading }}</h2>
                            <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed">{{ $body }}</p>
                        </div>
                    @endforeach

                    <div class="py-8">
                        <h2 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-4">Getting in touch
                        </h2>
                        <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                            To ask what we hold about you, or to have it removed, please
                            <a href="{{ url('/contact-us') }}"
                                class="font-bold text-brand-700 dark:text-brand-300 underline underline-offset-4">contact
                                us</a>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
