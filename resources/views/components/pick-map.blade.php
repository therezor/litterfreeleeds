@props(['pick'])

{{-- Google Maps. With GOOGLE_MAPS_API_KEY set this uses the official Embed API;
     without one it falls back to the keyless embed, which works but is not a
     documented endpoint, so set a key before going live. --}}
@php
    $coordinates = $pick->latitude . ',' . $pick->longitude;
    $apiKey = config('services.google_maps.key');

    $embedUrl = $apiKey
        ? 'https://www.google.com/maps/embed/v1/place?' . http_build_query([
            'key' => $apiKey,
            'q' => $pick->formatted_postcode . ', UK',
            'center' => $coordinates,
            'zoom' => 15,
        ])
        : 'https://maps.google.com/maps?' . http_build_query([
            'q' => $coordinates,
            'z' => 15,
            'hl' => 'en',
            'output' => 'embed',
        ]);

    $fullMapUrl = 'https://www.google.com/maps/search/?' . http_build_query([
        'api' => 1,
        'query' => $coordinates,
    ]);
@endphp

<figure {{ $attributes->merge(['class' => 'border-2 border-ink-950 dark:border-white/20']) }}>
    <iframe src="{{ $embedUrl }}" title="Map showing the meeting point for {{ $pick->name }}" loading="lazy"
        allowfullscreen referrerpolicy="no-referrer-when-downgrade"
        class="block w-full h-[22rem] sm:h-[28rem]"></iframe>

    <figcaption
        class="flex flex-wrap items-center justify-between gap-3 border-t-2 border-ink-950 dark:border-white/20 px-5 py-3 text-sm bg-white dark:bg-ink-900">
        <span class="text-ink-950/70 dark:text-brand-100/70">
            {{ $pick->location }}, <span class="font-semibold text-ink-950 dark:text-white">{{ $pick->formatted_postcode }}</span>
        </span>
        <a href="{{ $fullMapUrl }}" target="_blank" rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 font-bold uppercase tracking-wider text-xs text-brand-700 dark:text-brand-300 hover:underline underline-offset-4">
            Larger map
            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" aria-hidden="true" />
        </a>
    </figcaption>
</figure>
