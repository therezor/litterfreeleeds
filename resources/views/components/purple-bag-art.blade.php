{{-- A tied purple bag at a collection point. Flat two-tone screenprint to match
     the litter-picker scene and the rest of the poster language.

     The bag reads as a *tied sack*, not a container: gathered pinch at the neck,
     a knot above it with the loose ends flopping over, and gather creases
     fanning down the shoulders. Light falls from the left, so the shadow face
     sits right of centre on both the body and the knot.

     The leaf printed on the bag is a leaf, not the logo — the mark in
     logo-mark.blade.php is a bare branch. Say the word and this becomes the
     branch too; it is kept as a leaf here because a leaf reads at this size and
     carries the "looking after the place" idea on its own.

     The same paths appear in litter-picker-scene.blade.php inside a scaled
     group — edit both together. --}}
<svg {{ $attributes->merge(['class' => 'w-full h-auto']) }} viewBox="0 0 260 260" fill="none"
    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

    {{-- flat poster disc --}}
    <circle cx="130" cy="132" r="118" fill="currentColor" opacity="0.10" />

    {{-- ground bar --}}
    <rect x="18" y="226" width="224" height="7" fill="currentColor" opacity="0.85" />

    {{-- twisted neck, drawn first so the knot and cinch cover both its ends --}}
    <path d="M120 78C123 70 123 62 121 52L139 52C137 62 137 70 140 78Z" fill="#9b4391" />

    {{-- loose ends flopping out of the knot; roots sit under the knot ball --}}
    <path d="M126 47C110 43 95 49 87 61c-3 5 2 8 5 3 7-10 19-14 34-11Z" fill="#d372c7" />
    <path d="M134 47c16-4 31 2 39 14 3 5-2 8-5 3-7-10-19-14-34-11Z" fill="#9b4391" />

    {{-- the knot itself: lit ball + shadow ball, split right of centre --}}
    <path d="M136 38C119 38 106 46 106 56s13 17 30 17Z" fill="#ba4eae" />
    <path d="M136 38c14 0 19 8 19 18s-5 17-19 17Z" fill="#88337f" />
    <path d="M120 45c6 6 11 13 14 20" stroke="#6b2664" stroke-width="4" stroke-linecap="round" />

    {{-- gathered cinch below the knot --}}
    <path d="M116 72C122 79 138 79 144 72L143 88C137 93 123 93 117 88Z" fill="#6b2664" />

    {{-- lit face --}}
    <path d="M117 82C109 93 96 106 88 124 78 146 72 172 72 198c0 19 7 30 20 30h76c13 0 20-11 20-30 0-26-6-52-16-74-8-18-21-31-29-42Z"
        fill="#ba4eae" />
    {{-- shadow face --}}
    <path d="M143 82c7 28 12 68 9 108-1 20-2 32-3 38h19c13 0 20-11 20-30 0-26-6-52-16-74-8-18-21-31-29-42Z"
        fill="#88337f" />

    {{-- gather creases fanning down from the cinch --}}
    <g stroke="#9b4391" stroke-width="3.5" stroke-linecap="round">
        <path d="M113 88C105 101 99 113 96 128" />
        <path d="M123 86C118 101 114 113 112 130" />
    </g>
    <g stroke="#6b2664" stroke-width="3.5" stroke-linecap="round">
        <path d="M147 86C152 100 155 112 157 129" />
        <path d="M156 90C162 102 167 112 169 127" />
    </g>

    {{-- leaf mark --}}
    <g transform="translate(87 139) scale(1.9)">
        <rect x="19.1" y="20" width="1.8" height="13.6" rx="0.9" fill="#fdf2fb" />
        <path d="M20 5.8c8.6 5.6 8.6 17.4 0 23-8.6-5.6-8.6-17.4 0-23z" fill="#fdf2fb" />
        <g stroke="#ba4eae" stroke-width="1.35" stroke-linecap="round">
            <path d="M20 27.2V9.4" />
            <path d="M20 15.4l5-3.4M20 21l5.2-3.5" />
            <path d="M20 18.2l-5-3.4M20 23.8l-5.2-3.5" />
        </g>
    </g>
</svg>
