<x-layouts.app title="Welcome to Litter Free Leeds"
    description="You're signed up. Here's what happens next, and how to use a purple bag.">

    <x-page-hero eyebrow="You're in" heading="Welcome, {{ $name }}"
        subheading="You've joined thousands of volunteers keeping Leeds clean. Two things before you start." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">

            <div class="grid sm:grid-cols-2 gap-8 max-w-3xl mb-16">
                <div class="border-2 border-ink-950 dark:border-white/25 p-7 shadow-hard dark:shadow-hard-light">
                    <x-heroicon-o-envelope-open class="w-8 h-8 text-brand-700 dark:text-brand-300 mb-4"
                        aria-hidden="true" />
                    <h2 class="display text-xl text-ink-950 dark:text-white mb-3">Check your inbox</h2>
                    <p class="text-sm text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        We've sent you an email with a link to confirm your address. Nothing else happens until you
                        click it — including telling your Purple Bag Holder you've joined.
                    </p>
                </div>

                <div class="border-2 border-ink-950 dark:border-white/25 p-7 shadow-hard dark:shadow-hard-light">
                    <x-heroicon-o-user-group class="w-8 h-8 text-brand-700 dark:text-brand-300 mb-4"
                        aria-hidden="true" />
                    <h2 class="display text-xl text-ink-950 dark:text-white mb-3">Your Purple Bag Holder</h2>
                    <p class="text-sm text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        @if ($hasBagHolder)
                            Once you've confirmed your email, your local Purple Bag Holder will contact you soon.
                            They're a volunteer near you who keeps a stock of purple bags and hands them out locally.
                        @else
                            We're still building our network of bag holders in your area, so this may take a little
                            longer than usual. Someone will contact you soon to sort you out with purple bags.
                        @endif
                    </p>
                </div>
            </div>

            <div class="border-t-2 border-ink-950 dark:border-white/25 pt-12">
                <p
                    class="flex items-center gap-3 text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-6">
                    <span class="w-10 h-0.5 bg-brand-600 dark:bg-brand-400"></span>
                    Before your first pick
                </p>

                <x-purple-bag-conditions />

                <div class="mt-12 flex flex-wrap gap-4">
                    <x-action-button :href="url('/upcoming-picks')" variant="primary">
                        Find a pick near you
                    </x-action-button>
                    <x-action-button :href="route('purple-bag-conditions')" variant="outline">
                        Save these conditions
                    </x-action-button>
                </div>
            </div>

        </div>
    </section>

</x-layouts.app>
