@props(['href' => '#', 'variant' => 'primary'])

{{-- Square poster block with a hard offset shadow — the site's button language.
     Variants: primary (brand fill), invert (white on dark), outline (rule only). --}}
@php
    $base = 'group inline-flex items-center gap-3 px-7 py-3.5 text-sm sm:text-base font-bold uppercase tracking-wider transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';

    $variants = [
        'primary' => 'bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 shadow-hard dark:shadow-hard-light hover:shadow-hard-sm dark:hover:shadow-hard-light-sm hover:translate-x-[3px] hover:translate-y-[3px] focus-visible:ring-brand-600',
        'invert' => 'bg-white text-ink-950 shadow-hard-ink hover:shadow-hard-ink-sm hover:translate-x-[3px] hover:translate-y-[3px] focus-visible:ring-white',
        'outline' => 'border-2 border-ink-950 dark:border-white/40 text-ink-950 dark:text-white hover:bg-ink-950 hover:text-white dark:hover:bg-white dark:hover:text-ink-950 focus-visible:ring-brand-600',
    ];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
    <x-heroicon-o-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
</a>
