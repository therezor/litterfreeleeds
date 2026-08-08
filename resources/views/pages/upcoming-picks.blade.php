<x-layouts.app title="Upcoming Picks" active="upcoming-picks"
    description="Upcoming Litter Free Leeds community litter picks across the City of Leeds.">

    <x-page-hero eyebrow="Upcoming picks" heading="Find a pick near you"
        subheading="Our volunteers go out all year round, across all 50+ groups in the City of Leeds." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24 space-y-16">

            <x-postcode-search :value="$searchedPostcode" :failed="$searchFailed" :sorted="$isDistanceSorted" />

            @forelse ($monthlyPicks as $month => $picks)
                <div class="grid lg:grid-cols-12 gap-8 lg:gap-16">
                    <h2
                        class="lg:col-span-3 display text-2xl sm:text-3xl text-ink-950 dark:text-white lg:sticky lg:top-28 self-start">
                        {{ $month }}
                    </h2>

                    <ul class="lg:col-span-9 border-t-2 border-ink-950/12 dark:border-white/12">
                        @foreach ($picks as $pick)
                            <li class="border-b-2 border-ink-950/12 dark:border-white/12">
                                <x-pick-row :pick="$pick" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                {{-- No picks published yet. Keep the original placeholder copy —
                     it is still the right answer, just no longer the whole page. --}}
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    <div class="lg:col-span-7">
                        <div class="border-2 border-dashed border-ink-950/25 dark:border-white/20 p-10 sm:p-14 tag-corner">
                            <x-heroicon-o-calendar-days class="w-12 h-12 text-brand-600 dark:text-brand-400 mb-6" />
                            <h2 class="display text-3xl sm:text-4xl xl:text-5xl text-ink-950 dark:text-white">
                                Dates are published here soon
                            </h2>
                            <p class="mt-6 text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed max-w-xl">
                                Nothing is on the calendar just now. Get in touch and we'll let you know what's
                                happening near you — or just pick up litter as part of your regular walk.
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
            @endforelse

            @if ($archivePicks->isNotEmpty())
                {{-- <details> gives collapsed-by-default and keyboard support with
                     no JavaScript at all. --}}
                <details class="group/archive border-t-2 border-ink-950/12 dark:border-white/12 pt-8">
                    <summary
                        class="flex items-center gap-3 cursor-pointer list-none display text-2xl sm:text-3xl text-ink-950 dark:text-white hover:text-brand-700 dark:hover:text-brand-300 transition-colors">
                        <x-heroicon-o-chevron-right
                            class="w-6 h-6 shrink-0 transition-transform group-open/archive:rotate-90" aria-hidden="true" />
                        Recent picks
                    </summary>

                    <ul class="mt-8 border-t-2 border-ink-950/12 dark:border-white/12">
                        @foreach ($archivePicks as $pick)
                            <li class="border-b-2 border-ink-950/12 dark:border-white/12">
                                <x-pick-row :pick="$pick" muted />
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

        </div>
    </section>

</x-layouts.app>
