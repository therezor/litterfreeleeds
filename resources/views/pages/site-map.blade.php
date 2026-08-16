@php
    $sections = [
        'Main pages' => [
            ['label' => 'Home', 'url' => url('/'), 'detail' => 'Who we are and how to join in'],
            ['label' => 'Upcoming Picks', 'url' => url('/upcoming-picks'), 'detail' => 'Find a pick near you'],
            ['label' => 'About', 'url' => url('/about'), 'detail' => 'The group, the bags, the wildlife'],
            ['label' => 'Contact Us', 'url' => url('/contact-us'), 'detail' => 'Get started as a volunteer'],
        ],
        'Volunteer area' => [
            ['label' => 'Log In', 'url' => url('/app/login'), 'detail' => 'Access your dashboard'],
            ['label' => 'Create Account', 'url' => route('join.create'), 'detail' => 'Register as a volunteer'],
            ['label' => 'Purple Bag Conditions', 'url' => route('purple-bag-conditions'), 'detail' => 'How to use purple bags safely'],
        ],
        'Legal' => [
            ['label' => 'Privacy Policy', 'url' => url('/privacy-policy'), 'detail' => 'How we handle your data'],
            ['label' => 'Site Map', 'url' => url('/site-map'), 'detail' => 'This page'],
        ],
    ];
@endphp

<x-layouts.app title="Site Map" description="Every page on the Litter Free Leeds website.">

    <x-page-hero eyebrow="Site map" heading="Everything on this site"
        subheading="A full index of the Litter Free Leeds website." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24 space-y-16">

            @foreach ($sections as $heading => $items)
                <div class="grid lg:grid-cols-12 gap-8 lg:gap-16">
                    <h2
                        class="lg:col-span-3 display text-2xl sm:text-3xl text-ink-950 dark:text-white lg:sticky lg:top-28 self-start">
                        {{ $heading }}
                    </h2>

                    <ul class="lg:col-span-9 border-t-2 border-ink-950/12 dark:border-white/12">
                        @foreach ($items as $item)
                            <li class="border-b-2 border-ink-950/12 dark:border-white/12">
                                <a href="{{ $item['url'] }}"
                                    class="group flex items-center justify-between gap-6 py-6 hover:px-3 transition-all">
                                    <span>
                                        <span
                                            class="display block text-xl sm:text-2xl text-ink-950 dark:text-white group-hover:text-brand-700 dark:group-hover:text-brand-300 transition-colors">{{ $item['label'] }}</span>
                                        <span
                                            class="block mt-1 text-ink-950/60 dark:text-brand-100/60">{{ $item['detail'] }}</span>
                                    </span>
                                    <x-heroicon-o-arrow-right
                                        class="w-6 h-6 shrink-0 text-ink-950/30 dark:text-white/30 group-hover:text-brand-700 dark:group-hover:text-brand-300 group-hover:translate-x-1 transition-all" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

        </div>
    </section>

</x-layouts.app>
