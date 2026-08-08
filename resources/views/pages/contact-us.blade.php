<x-layouts.app title="Contact Us" active="contact-us"
    description="Get in touch with Litter Free Leeds to join a group, request bags, or start picking in your neighbourhood.">

    <x-page-hero eyebrow="Contact us" heading="Want to join in?"
        subheading="Whatever time you have available, however small, will be very much appreciated." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">

                <div class="lg:col-span-5">
                    <h2 class="display text-3xl sm:text-4xl xl:text-5xl text-ink-950 dark:text-white">
                        Tell us roughly where you are
                    </h2>
                    <p class="mt-6 text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        Registering lets us point you at a group near you and sort you out with bags. Whether you want
                        to join a group or just pick up litter as part of your regular walk, we can help you get
                        started.
                    </p>
                </div>

                <div class="lg:col-span-7">
                    <div class="grain relative overflow-hidden bg-ink-950 p-10 sm:p-12 tag-corner">
                        <div class="grain-layer opacity-[0.16] mix-blend-overlay" aria-hidden="true"></div>

                        <div class="relative">
                            <x-heroicon-o-envelope class="w-10 h-10 text-brand-300 mb-6" />
                            <h3 class="display text-3xl sm:text-4xl text-white">Create a volunteer account</h3>
                            <p class="mt-5 text-brand-100/75 leading-relaxed max-w-lg">
                                It takes a minute, and it's how we keep track of the bags coming back in from across the
                                city.
                            </p>
                            <div class="mt-8">
                                <x-action-button :href="url('/app/register')" variant="invert">
                                    Create an account
                                </x-action-button>
                            </div>
                            <p class="mt-8 text-sm text-brand-100/55">
                                Already registered?
                                <a href="{{ url('/app/login') }}"
                                    class="font-bold text-brand-300 hover:text-white underline underline-offset-4 transition-colors">Log
                                    in to your dashboard</a>.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
