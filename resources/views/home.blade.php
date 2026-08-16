@php
    // Only figures the source copy actually supports — no invented counts.
    $stats = [
        ['figure' => '50+', 'label' => 'Community groups', 'detail' => 'Picking across the whole City of Leeds'],
        ['figure' => '1,000+', 'label' => 'Active volunteers', 'detail' => 'Grown steadily over several years'],
        ['figure' => 'All year', 'label' => 'Out picking', 'detail' => 'Rain or shine, every month'],
    ];

    $mission = [
        [
            'icon' => 'heroicon-o-globe-europe-africa',
            'kicker' => '01',
            'title' => 'Protecting wildlife',
            'body' => 'Wildlife so often become the victims of littered items. Let\'s help protect them in their natural habitats — every bag counts.',
        ],
        [
            'icon' => 'heroicon-o-users',
            'kicker' => '02',
            'title' => 'Community driven',
            'body' => 'We have been an active group of volunteers for several years and have steadily grown our groups to thousands of people.',
        ],
        [
            'icon' => 'heroicon-o-sparkles',
            'kicker' => '03',
            'title' => 'Good for you, too',
            'body' => 'It\'s a great way to meet like minded people and to get some exercise while cleaning up where you live.',
        ],
    ];

    $steps = [
        [
            'title' => 'Get in touch',
            'body' => 'Tell us roughly where you are and how much time you have. Whatever time you have available, however small, will be very much appreciated.',
        ],
        [
            'title' => 'We sort the kit',
            'body' => 'We can provide bags and try to provide other equipment where we can, so there is nothing to buy before you start.',
        ],
        [
            'title' => 'Pick when it suits you',
            'body' => 'Join a group or just pick up litter as part of your regular walk. Our volunteers go out all year round — we can help you get started.',
        ],
    ];

    $ticker = ['Every bag counts', 'Leeds goes purple', '50+ community groups', 'All year round', 'Bags provided free'];
@endphp

<x-layouts.app active="home">

    {{-- ── Hero: oversized display type, illustration bleeding off the right ── --}}
    <section class="grain relative overflow-hidden bg-neutral-50 dark:bg-ink-950">
        <div class="grain-layer opacity-[0.10] dark:opacity-[0.14] mix-blend-multiply dark:mix-blend-overlay"
            aria-hidden="true"></div>
        <div class="absolute -top-40 -right-40 w-[46rem] h-[46rem] rounded-full bg-brand-200/50 dark:bg-brand-700/20 blur-3xl"
            aria-hidden="true"></div>

        <div class="relative max-w-[92rem] mx-auto px-5 sm:px-8">
            <div class="grid lg:grid-cols-12 gap-8 items-center pt-12 pb-14 lg:pt-16 lg:pb-20">

                <div class="lg:col-span-7 xl:col-span-6">
                    <p
                        class="flex items-center gap-3 text-xs sm:text-sm font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-6">
                        <span class="w-10 h-0.5 bg-brand-600 dark:bg-brand-400"></span>
                        Volunteer litter picking · Leeds
                    </p>

                    <h1 class="display text-ink-950 dark:text-white text-[3.2rem] sm:text-[4.4rem] xl:text-[5.6rem]">
                        We can truly<br />
                        make a
                        <span class="relative inline-block text-brand-600 dark:text-brand-300">
                            difference
                            <span
                                class="absolute left-0 -bottom-1 sm:-bottom-2 w-full h-[0.14em] bg-brand-600 dark:bg-brand-400"
                                aria-hidden="true"></span>
                        </span>
                    </h1>

                    <p class="mt-8 max-w-xl text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        Over 50 volunteer groups go out litter picking across the whole City of Leeds — all year round.
                        Grab a purple bag and join them.
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        <a href="{{ route('join.create') }}"
                            class="group inline-flex items-center gap-3 px-8 py-4 bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 text-base font-bold uppercase tracking-wider shadow-hard dark:shadow-hard-light hover:shadow-hard-sm dark:hover:shadow-hard-light-sm hover:translate-x-[3px] hover:translate-y-[3px] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                            Join us now
                            <x-heroicon-o-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                        {{-- pt-1.5 balances pb-1 + border-b-2 (6px). Without it the
                             box is bottom-heavy and items-center leaves this label
                             sitting 3px above the button's. --}}
                        <a href="{{ url('/upcoming-picks') }}"
                            class="inline-flex items-center gap-2 pt-1.5 pb-1 text-base font-bold uppercase tracking-wider text-ink-950 dark:text-white border-b-2 border-ink-950/30 dark:border-white/30 hover:border-brand-600 dark:hover:border-brand-400 transition-colors">
                            See upcoming picks
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 lg:-mr-4 xl:-mr-8">
                    <div class="text-brand-950 dark:text-brand-100">
                        <x-litter-picker-scene />
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Ticker ───────────────────────────────────────────────────────── --}}
    <div class="bg-ink-950 border-y-2 border-ink-950 dark:border-white/15 overflow-hidden py-3.5"
        aria-hidden="true">
        <div class="marquee-inner">
            {{-- Two identical tracks: the second hides the seam as the first scrolls out. --}}
            @for ($track = 0; $track < 2; $track++)
                <div class="flex shrink-0 items-center">
                    @foreach ($ticker as $item)
                        <span class="display text-xl text-brand-100 px-7">{{ $item }}</span>
                        <span class="text-brand-400 text-xl leading-none">✦</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    {{-- ── Stats: editorial rules, not cards ────────────────────────────── --}}
    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-16 sm:py-20">
            <dl class="grid grid-cols-1 sm:grid-cols-3">
                @foreach ($stats as $index => $stat)
                    <div
                        class="py-8 sm:py-0 sm:px-8 first:sm:pl-0 last:sm:pr-0 border-t sm:border-t-0 sm:border-l border-ink-950/15 dark:border-white/15 first:border-t-0 first:sm:border-l-0">
                        <dd class="display text-5xl sm:text-6xl xl:text-7xl text-brand-700 dark:text-brand-300 flex items-end min-h-[3.5rem] sm:min-h-[4.25rem] xl:min-h-[5rem]">
                            {{ $stat['figure'] }}
                        </dd>
                        <dt class="mt-4 text-lg font-bold text-ink-950 dark:text-white">{{ $stat['label'] }}</dt>
                        <dd class="mt-1 text-ink-950/60 dark:text-brand-100/60">{{ $stat['detail'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ── Mission: asymmetric split, numbered rows, no card chrome ─────── --}}
    <section class="bg-neutral-50 dark:bg-ink-950 border-t-2 border-ink-950 dark:border-white/15">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-28">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                {{-- Sticky so the heading tracks the rows instead of leaving a tall
                     empty column beside them. --}}
                <div class="lg:col-span-5 lg:sticky lg:top-28 self-start">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-5">
                        Our mission
                    </p>
                    <h2 class="display text-4xl sm:text-5xl xl:text-6xl text-ink-950 dark:text-white">
                        Every bag counts across the whole of Leeds
                    </h2>
                    <div class="mt-8 w-24 h-1 bg-brand-600 dark:bg-brand-400"></div>

                    <a href="{{ url('/about') }}"
                        class="mt-8 inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-ink-950 dark:text-white border-b-2 border-ink-950/25 dark:border-white/25 hover:border-brand-600 dark:hover:border-brand-400 pb-1 transition-colors">
                        Read more about us
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="lg:col-span-7 divide-y divide-ink-950/12 dark:divide-white/12">
                    @foreach ($mission as $card)
                        <article class="group flex gap-6 sm:gap-10 py-9 first:pt-0 last:pb-0">
                            <div class="shrink-0 w-12 sm:w-14 flex flex-col items-start gap-3">
                                <span
                                    class="display text-4xl sm:text-5xl leading-none text-brand-600/35 dark:text-brand-400/35 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                    {{ $card['kicker'] }}
                                </span>
                                <x-dynamic-component :component="$card['icon']"
                                    class="w-7 h-7 text-brand-600 dark:text-brand-400" />
                            </div>
                            <div>
                                <h3 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-3">
                                    {{ $card['title'] }}
                                </h3>
                                <p class="text-ink-950/70 dark:text-brand-100/65 leading-relaxed max-w-xl">
                                    {{ $card['body'] }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    {{-- ── The purple bag: full-bleed poster ────────────────────────────── --}}
    <section class="grain relative overflow-hidden bg-ink-950 border-y-2 border-ink-950">
        <div class="grain-layer opacity-[0.18] mix-blend-overlay" aria-hidden="true"></div>
        <div class="absolute -bottom-1/3 left-1/4 w-[38rem] h-[38rem] rounded-full bg-brand-500/20 blur-3xl"
            aria-hidden="true"></div>

        <div class="relative max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <div class="lg:col-span-5 order-last lg:order-first">
                    <div class="max-w-sm mx-auto lg:mx-0 text-brand-200">
                        <x-purple-bag-art />
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-300 mb-5">The purple bag</p>
                    <h2 class="display text-4xl sm:text-5xl xl:text-[4.2rem] text-white">
                        Wherever you see a purple bag, a volunteer put it there
                    </h2>
                    <p class="mt-7 max-w-xl text-lg text-brand-100/75 leading-relaxed">
                        Leave it at a collection point and it gets picked up — no van, no rota, no fuss. It's the small,
                        visible signal that somebody nearby chose to look after the place.
                    </p>
                    <a href="{{ url('/about') }}"
                        class="mt-8 inline-flex items-center gap-2 text-base font-bold uppercase tracking-wider text-brand-300 border-b-2 border-brand-300/40 hover:border-brand-300 pb-1 transition-colors">
                        More about how we work
                        <x-heroicon-o-arrow-right class="w-5 h-5" />
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Steps: offset rows, oversized numerals ───────────────────────── --}}
    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-28">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-5">
                Getting started
            </p>
            <h2 class="display text-4xl sm:text-5xl xl:text-6xl text-ink-950 dark:text-white max-w-3xl">
                Three steps, and you're picking
            </h2>

            <ol class="mt-14 space-y-px">
                @foreach ($steps as $index => $step)
                    {{-- Each row indents a little further than the last, so the block
                         steps down the page instead of sitting in a flat grid. --}}
                    <li class="group border-t-2 border-ink-950/12 dark:border-white/12 last:border-b-2 py-8 lg:py-10
                               {{ ['', 'lg:pl-16', 'lg:pl-32'][$index] }}">
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-4 sm:gap-10">
                            <span
                                class="display text-6xl sm:text-7xl text-brand-600/25 dark:text-brand-400/25 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors leading-none w-24 shrink-0">
                                0{{ $index + 1 }}
                            </span>
                            <div class="max-w-2xl">
                                <h3 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-3">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="text-ink-950/70 dark:text-brand-100/65 leading-relaxed">{{ $step['body'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ── Closing CTA ──────────────────────────────────────────────────── --}}
    <section class="grain relative overflow-hidden bg-brand-700 dark:bg-brand-800 border-t-2 border-ink-950">
        <div class="grain-layer opacity-[0.16] mix-blend-overlay" aria-hidden="true"></div>

        <div class="relative max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-28 text-center">
            <h2 class="display text-5xl sm:text-6xl xl:text-8xl text-white max-w-4xl mx-auto">
                Ready to make a difference?
            </h2>
            <p class="mt-7 max-w-2xl mx-auto text-lg text-brand-100/85 leading-relaxed">
                Whatever time you have available, however small, will be very much appreciated.
                Every bag counts towards a cleaner Leeds.
            </p>
            <a href="{{ url('/contact-us') }}"
                class="mt-10 inline-flex items-center gap-3 px-9 py-4 bg-white text-brand-950 text-base font-bold uppercase tracking-wider shadow-hard hover:shadow-hard-sm hover:translate-x-[3px] hover:translate-y-[3px] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700">
                Start volunteering today
                <x-heroicon-o-arrow-right class="w-5 h-5" />
            </a>
        </div>
    </section>

</x-layouts.app>
