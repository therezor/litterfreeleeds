<footer class="bg-ink-950 border-t-2 border-ink-950 dark:border-white/15 text-brand-100">
    <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-16">

        <div class="grid gap-12 lg:grid-cols-12">

            <div class="lg:col-span-5">
                <div class="flex items-center gap-3 mb-5">
                    <x-brand-mark class="w-11 h-11" />
                    <span class="display text-3xl text-white leading-none">Litter&nbsp;Free&nbsp;Leeds</span>
                </div>
                <p class="max-w-sm text-brand-100/60 leading-relaxed">
                    A volunteer group of thousands, picking litter across the whole City of Leeds — all year round.
                </p>
            </div>

            <div class="lg:col-span-4">
                <h2 class="text-xs font-bold uppercase tracking-[0.22em] text-brand-300 mb-5">Explore</h2>
                <nav class="grid grid-cols-2 gap-y-3 gap-x-6">
                    @foreach ([
        'Home' => '/',
        'Upcoming Picks' => '/upcoming-picks',
        'About' => '/about',
        'Contact Us' => '/contact-us',
        'Privacy Policy' => '/privacy-policy',
        'Site Map' => '/site-map',
    ] as $label => $path)
                        <a href="{{ url($path) }}"
                            class="text-brand-100/70 hover:text-white transition-colors">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-bold uppercase tracking-[0.22em] text-brand-300 mb-5">Join in</h2>
                <a href="{{ url('/app/register') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 border-2 border-brand-300 text-brand-100 text-sm font-bold uppercase tracking-wider hover:bg-brand-300 hover:text-brand-950 transition-colors">
                    Become a volunteer
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>

        </div>

        <div
            class="mt-14 pt-8 border-t border-white/12 flex flex-wrap items-center justify-between gap-4 text-sm text-brand-100/50">
            <p>&copy; {{ date('Y') }} Litter Free Leeds. All Rights Reserved.</p>
            <p class="flex items-center gap-1.5">
                Made with <x-heroicon-s-heart class="w-4 h-4 text-brand-400" /> by volunteers
            </p>
        </div>
    </div>
</footer>
