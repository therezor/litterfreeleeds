{{--
    The animated litter picker. A grabber descends, closes on a piece of litter,
    lifts it, swings over the purple bag and drops it in — then resets.

    Drawn flat: solid two-tone fills (lit face + shadow face), no gradients, no
    blur, no soft shadows — the same screenprint language as the square buttons
    and 2px rules. The backdrop disc and ground use currentColor so the scene
    adapts to whichever surface it sits on.

    Motion lives in resources/css/app.css (.lp-* classes) so it can be disabled
    wholesale under prefers-reduced-motion; the static frame below still reads as
    a complete illustration with no animation at all.
--}}
<svg class="lp-scene w-full h-auto" viewBox="0 0 440 380" fill="none" xmlns="http://www.w3.org/2000/svg"
    role="img" aria-label="A litter picker grabs a piece of litter from the ground and drops it into a purple collection bag.">

    {{-- Flat poster disc behind the bag. cx + r must stay inside the 440-unit
         viewBox or the SVG viewport shears a flat edge off the disc's right. --}}
    <circle cx="326" cy="212" r="110" fill="currentColor" opacity="0.10" />

    {{-- Ground: a solid bar, not a soft ellipse --}}
    <rect x="14" y="302" width="412" height="7" fill="currentColor" opacity="0.85" />

    {{-- Litter left behind, for context --}}
    <rect x="40" y="288" width="30" height="14" fill="currentColor" opacity="0.35" />
    <path d="M150 302l6-13 14 4 10-7 3 16z" fill="currentColor" opacity="0.28" />

    {{-- The purple bag, open-mouthed: this is the bag being filled, whereas
         components/purple-bag-art.blade.php is the same bag knotted shut and
         left at a collection point. The two share the palette, the leaf mark and
         the two-tone/crease idiom, but the paths are their own — a change to one
         does not propagate to the other.

         Drawn in the other component's 260-unit space so the numbers stay
         comparable, then placed by the inner group's transform: the bag's centre
         lands on x=330 and its base on y=302 (the ground bar), which is what
         .lp-bag's transform-origin assumes. The mouth is three layers: far rim,
         dark opening, then the body, whose wavy top edge is the near rim biting
         into the opening. --}}
    <g class="lp-bag">
        <g transform="translate(241.6 146.96) scale(0.68)">
            {{-- far rim, cut unevenly the way sheet plastic is --}}
            <path d="M96 74C99 64 108 58 117 59 125 60 127 55 135 56c8 1 13 6 19 7 6 1 10 5 10 11z" fill="#9b4391" />
            {{-- the opening --}}
            <ellipse cx="130" cy="74" rx="34" ry="12" fill="#551f4f" />

            {{-- lit face; its top edge is the near rim --}}
            <path
                d="M96 74c5 9 11 13 18 12 7-1 8 3 16 3 8 0 11-4 18-3 7 1 12-3 16-12 6 22 16 42 20 66 4 24 4 50 2 66-1 14-8 22-22 22H96c-14 0-21-8-22-22-2-16-2-42 2-66 4-24 14-44 20-66Z"
                fill="#ba4eae" />
            {{-- shadow face --}}
            <path
                d="M148 86c7 1 12-3 16-12 6 22 16 42 20 66 4 24 4 50 2 66-1 14-8 22-22 22h-11c1-6 2-18 3-38 2-36-2-77-8-104Z"
                fill="#88337f" />

            {{-- gather creases running down from the mouth --}}
            <g stroke="#9b4391" stroke-width="3.5" stroke-linecap="round">
                <path d="M106 96C100 110 96 124 94 140" />
                <path d="M119 96C115 110 112 124 111 142" />
            </g>
            <g stroke="#6b2664" stroke-width="3.5" stroke-linecap="round">
                <path d="M153 96C157 110 160 124 161 142" />
                <path d="M164 98C169 112 173 124 175 140" />
            </g>

            {{-- leaf mark --}}
            <g transform="translate(87 145) scale(1.9)">
                <rect x="19.1" y="20" width="1.8" height="13.6" rx="0.9" fill="#fdf2fb" />
                <path d="M20 5.8c8.6 5.6 8.6 17.4 0 23-8.6-5.6-8.6-17.4 0-23z" fill="#fdf2fb" />
                <g stroke="#ba4eae" stroke-width="1.35" stroke-linecap="round">
                    <path d="M20 27.2V9.4" />
                    <path d="M20 15.4l5-3.4M20 21l5.2-3.5" />
                    <path d="M20 18.2l-5-3.4M20 23.8l-5.2-3.5" />
                </g>
            </g>
        </g>
    </g>

    {{-- Impact ticks, flicked off the bag mouth as the piece lands. Placed clear
         of the knot and its loose ends. --}}
    <g class="lp-impact" stroke="#e99edd" stroke-width="5" stroke-linecap="round" opacity="0">
        <path d="M294 182l-15-12" />
        <path d="M330 168v-16" />
        <path d="M366 182l15-12" />
    </g>

    {{-- Piece of litter being carried --}}
    <g class="lp-litter">
        <rect x="92" y="284" width="26" height="18" fill="#e11d48" />
        <rect x="92" y="284" width="26" height="6" fill="#fda4af" />
    </g>

    {{-- The picker. Drawn low enough in the viewBox that the grip stays on canvas
         when the arm lifts by 76px. --}}
    <g class="lp-arm">
        {{-- grip --}}
        <rect x="88" y="86" width="32" height="54" fill="#551f4f" />
        <rect x="88" y="86" width="13" height="54" fill="#88337f" />
        {{-- trigger --}}
        <path d="M120 108h11l6 14-9 3z" fill="#9b4391" />
        {{-- shaft --}}
        <rect x="97" y="140" width="14" height="114" fill="#88337f" />
        <rect x="97" y="140" width="5" height="114" fill="#d372c7" />
        <rect x="95" y="192" width="18" height="12" fill="#551f4f" />
        {{-- jaws, drawn open and wide enough to straddle the litter --}}
        <g stroke="#6b2664" stroke-width="11" stroke-linecap="butt" fill="none">
            <path class="lp-jaw-left" d="M104 252l-24 40" />
            <path class="lp-jaw-right" d="M104 252l24 40" />
        </g>
        <rect x="94" y="243" width="20" height="16" fill="#9b4391" />
    </g>
</svg>
