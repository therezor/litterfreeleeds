@props(['pick', 'muted' => false])

{{-- One pick as an editorial rule row, matching the site-map list language —
     no card chrome, the divider does the work. --}}
@php
    $distance = $pick->distance_miles ?? null;
@endphp

<a href="{{ route('upcoming-picks.show', $pick) }}"
    @class([
        'group flex items-start justify-between gap-6 py-6 hover:px-3 transition-all',
        'opacity-70 hover:opacity-100' => $muted,
    ])>
    <span class="min-w-0">
        <span class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <span
                class="display text-xl sm:text-2xl text-ink-950 dark:text-white group-hover:text-brand-700 dark:group-hover:text-brand-300 transition-colors">
                {{ $pick->name }}
            </span>

            @if ($distance !== null)
                <span class="text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
                    {{ \Illuminate\Support\Number::format($distance, maxPrecision: 1) }} miles away
                </span>
            @endif
        </span>

        <span
            class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm font-semibold text-ink-950/60 dark:text-brand-100/60">
            <span class="inline-flex items-center gap-1.5">
                <x-heroicon-o-calendar-days class="w-4 h-4 shrink-0" aria-hidden="true" />
                <span>{{ $pick->date->translatedFormat('D j M') }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" aria-hidden="true" />
                <span>{{ $pick->time_range }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <x-heroicon-o-map-pin class="w-4 h-4 shrink-0" aria-hidden="true" />
                <span>{{ $pick->location }}, {{ $pick->formatted_postcode }}</span>
            </span>
            @if ($pick->place_label)
                <span class="text-brand-700 dark:text-brand-300">{{ $pick->place_label }}</span>
            @endif
        </span>

        <span class="block mt-2 text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
            {{ $pick->excerpt }}
        </span>
    </span>

    <x-heroicon-o-arrow-right
        class="w-6 h-6 mt-1 shrink-0 text-ink-950/30 dark:text-white/30 group-hover:text-brand-700 dark:group-hover:text-brand-300 group-hover:translate-x-1 transition-all" />
</a>
