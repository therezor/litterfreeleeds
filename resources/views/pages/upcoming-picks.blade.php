<x-layouts.app title="Upcoming Picks" active="upcoming-picks"
    description="Upcoming Litter Free Leeds community litter picks across the City of Leeds.">

    <x-page-hero eyebrow="Upcoming picks" heading="Find a pick near you"
        subheading="Our volunteers go out all year round, across all 50+ groups in the City of Leeds." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <div class="lg:col-span-7">
                    <div
                        class="border-2 border-dashed border-ink-950/25 dark:border-white/20 p-10 sm:p-14 tag-corner">
                        <x-heroicon-o-calendar-days class="w-12 h-12 text-brand-600 dark:text-brand-400 mb-6" />
                        <h2 class="display text-3xl sm:text-4xl xl:text-5xl text-ink-950 dark:text-white">
                            Dates are published here soon
                        </h2>
                        <p class="mt-6 text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed max-w-xl">
                            We're moving our pick calendar onto the site. In the meantime, get in touch and we'll let
                            you know what's happening near you — or just pick up litter as part of your regular walk.
                        </p>
                        <div class="mt-9">
                            <x-action-button :href="url('/contact-us')">Get in touch</x-action-button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="max-w-xs mx-auto text-brand-950 dark:text-brand-100">
                        <x-purple-bag-art />
                    </div>
                    <p
                        class="mt-6 text-center text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
                        Bags provided free
                    </p>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
