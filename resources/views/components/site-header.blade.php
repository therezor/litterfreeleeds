@props(['active' => null])

@php
    $links = [
        'home' => ['label' => 'Home', 'url' => url('/')],
        'upcoming-picks' => ['label' => 'Upcoming Picks', 'url' => url('/upcoming-picks')],
        'about' => ['label' => 'About', 'url' => url('/about')],
        'contact-us' => ['label' => 'Contact Us', 'url' => url('/contact-us')],
    ];

    // Underline-on-rule nav rather than pill buttons — pills are the tell.
    $base = 'relative py-1 text-[0.95rem] font-semibold tracking-wide transition-colors';
    $on = $base . ' text-ink-950 dark:text-white after:absolute after:-bottom-px after:left-0 after:right-0 after:h-[3px] after:bg-brand-600 dark:after:bg-brand-400';
    $off = $base . ' text-ink-950/55 dark:text-brand-100/55 hover:text-ink-950 dark:hover:text-white';
@endphp

<header x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
    class="sticky top-0 z-50 bg-white/90 dark:bg-ink-950/90 backdrop-blur-md border-b-2 border-ink-950 dark:border-white/15 transition-shadow"
    :class="scrolled && 'shadow-[0_6px_0_-2px_rgba(0,0,0,0.14)]'">

    <div class="max-w-[92rem] mx-auto px-5 sm:px-8">
        <div class="flex items-center justify-between h-[4.5rem] gap-6">

            <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                <x-brand-mark class="w-10 h-10" />
                <span class="display text-2xl sm:text-[1.7rem] text-ink-950 dark:text-white leading-none">
                    Litter&nbsp;Free&nbsp;Leeds
                </span>
            </a>

            <nav class="hidden lg:flex items-center gap-8">
                @foreach ($links as $key => $link)
                    <a href="{{ $link['url'] }}" @class([$on => $active === $key, $off => $active !== $key])
                        @if ($active === $key) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <button @click="darkMode = !darkMode" type="button"
                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                    class="p-2 text-ink-950/60 dark:text-brand-100/60 hover:text-ink-950 dark:hover:text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                    <x-heroicon-s-moon x-cloak x-show="!darkMode" class="w-5 h-5" />
                    <x-heroicon-s-sun x-cloak x-show="darkMode" class="w-5 h-5" />
                </button>

                <a href="{{ auth()->check() ? url('/app') : url('/app/login') }}"
                    class="hidden sm:inline-flex items-center px-5 py-2.5 text-sm font-bold uppercase tracking-wider text-ink-950 dark:text-white border-2 border-ink-950 dark:border-white/40 hover:bg-ink-950 hover:text-white dark:hover:bg-white dark:hover:text-ink-950 transition-colors">
                    {{ auth()->check() ? 'Dashboard' : 'Log In' }}
                </a>

                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" aria-label="Toggle navigation"
                    :aria-expanded="mobileMenuOpen"
                    class="lg:hidden p-2 text-ink-950 dark:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                    <x-heroicon-o-bars-3 x-show="!mobileMenuOpen" class="w-7 h-7" />
                    <x-heroicon-o-x-mark x-cloak x-show="mobileMenuOpen" class="w-7 h-7" />
                </button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="mobileMenuOpen" x-transition
        class="lg:hidden absolute w-full left-0 bg-neutral-50 dark:bg-ink-950 border-b-2 border-ink-950 dark:border-white/15 shadow-xl">
        <div class="px-5 sm:px-8 py-4 divide-y divide-ink-950/10 dark:divide-white/10">
            @foreach ($links as $key => $link)
                <a href="{{ $link['url'] }}"
                    class="display block py-3 text-2xl {{ $active === $key ? 'text-brand-700 dark:text-brand-300' : 'text-ink-950 dark:text-white' }}">{{ $link['label'] }}</a>
            @endforeach
            <a href="{{ auth()->check() ? url('/app') : url('/app/login') }}"
                class="display block py-3 text-2xl text-ink-950 dark:text-white sm:hidden">{{ auth()->check() ? 'Dashboard' : 'Log In' }}</a>
        </div>
    </div>
</header>
