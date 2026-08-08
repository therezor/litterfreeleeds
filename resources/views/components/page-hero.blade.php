@props(['heading', 'subheading' => null, 'eyebrow' => null])

<section class="grain relative overflow-hidden bg-neutral-50 dark:bg-ink-950 border-b-2 border-ink-950 dark:border-white/15">
    <div class="grain-layer opacity-[0.10] dark:opacity-[0.14] mix-blend-multiply dark:mix-blend-overlay"
        aria-hidden="true"></div>
    <div class="absolute -top-52 -right-24 w-[34rem] h-[34rem] rounded-full bg-brand-200/55 dark:bg-brand-700/20 blur-3xl"
        aria-hidden="true"></div>

    <div class="relative max-w-[92rem] mx-auto px-5 sm:px-8 py-16 sm:py-24">
        @if ($eyebrow)
            <p class="flex items-center gap-3 text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-6">
                <span class="w-10 h-0.5 bg-brand-600 dark:bg-brand-400"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="display text-4xl sm:text-6xl xl:text-7xl text-ink-950 dark:text-white max-w-4xl">
            {{ $heading }}
        </h1>

        @if ($subheading)
            <p class="mt-7 max-w-2xl text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                {{ $subheading }}
            </p>
        @endif
    </div>
</section>
