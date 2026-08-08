<x-layouts.app title="About" active="about"
    description="Litter Free Leeds is a volunteer group of thousands, picking litter across the whole City of Leeds all year round.">

    <x-page-hero eyebrow="About us" heading="An active group of volunteers, several years in"
        subheading="We have steadily grown our groups to thousands of people, covering the whole of the City of Leeds." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                <div class="lg:col-span-4">
                    <h2 class="display text-3xl sm:text-4xl text-ink-950 dark:text-white">Why we do it</h2>
                    <div class="mt-6 w-20 h-1 bg-brand-600 dark:bg-brand-400"></div>
                </div>

                <div class="lg:col-span-8 max-w-3xl">
                    <div class="space-y-6 text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        <p>
                            We have over 50 groups who go out regularly litter picking throughout the whole of the City.
                            Wildlife so often become the victims of littered items — helping protect them in their
                            natural habitats is a large part of why we do this.
                        </p>
                        <p>
                            Wherever you see a purple bag, it has been collected by a volunteer. We can provide bags and
                            try to provide other equipment where we can, so there is nothing you need to buy before you
                            start.
                        </p>
                        <p>
                            Our volunteers go out all year round. Whether you are wanting to join a group or just want
                            to pick up litter as part of your regular walk, we can help you get started. It's a great
                            way to meet like minded people and to get some exercise while cleaning up where you live.
                        </p>
                    </div>

                    <p class="display mt-10 text-2xl sm:text-3xl text-ink-950 dark:text-white max-w-2xl">
                        Every bag counts in making a huge difference across the whole of the City of Leeds
                    </p>

                    <div class="mt-10 flex flex-wrap gap-4">
                        <x-action-button :href="url('/contact-us')">Get in touch</x-action-button>
                        <x-action-button :href="url('/upcoming-picks')" variant="outline">Upcoming picks</x-action-button>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
