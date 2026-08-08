{{--
    Mark shaped after the organisation's real logo: a solid black disc. Theirs
    sets the wordmark inside it, which is unreadable at favicon size, so this
    holds a single bare branch — a stem with three twigs off it, no leaves.

    Two colours and stroked forms only, so it survives down to 16px; the stem is
    heavier than the twigs so the hierarchy still reads once the strokes collapse
    to a pixel. Keep in sync with public/favicon.svg, which carries the same
    artwork for the browser tab and the Filament panel.

    Size comes from the caller (see brand-mark.blade.php); deliberately no
    default here, or the merge emits two conflicting w-*/h-* pairs.
--}}
<svg {{ $attributes }} viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="20" cy="20" r="20" fill="#0b0a0c" />

    <g stroke="#ffffff" stroke-linecap="round">
        {{-- stem: dead vertical --}}
        <path d="M20 33V9" stroke-width="3.4" />

        {{-- Two twigs, one per side, both 9:8 off vertical and the same length.
             Three twigs stacked the ink into the top of the disc; at two, the
             weighted centroid of all three strokes lands on (20, 20.06) — the
             disc centre — with bare stem left showing above and below. --}}
        <g stroke-width="3">
            <path d="M20 26L11 18" />
            <path d="M20 20L29 12" />
        </g>
    </g>
</svg>
