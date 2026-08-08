{{--
    The animated litter picker. Three pieces of litter lie on the ground; the
    grabber works along them one at a time — descends, closes, lifts, swings over
    the purple bag and drops the piece in — until the ground is clear, then the
    loop resets with fresh litter.

    Drawn flat: solid two-tone fills (lit face + shadow face), no gradients, no
    blur, no soft shadows — the same screenprint language as the square buttons
    and 2px rules. The backdrop disc and ground use currentColor so the scene
    adapts to whichever surface it sits on.

    Motion lives in resources/css/app.css (.lp-* classes) so it can be disabled
    wholesale under prefers-reduced-motion; the static frame below still reads as
    a complete illustration with no animation at all — the grabber stands over the
    first piece with its jaws open and all three pieces are on the ground.
--}}
<svg class="lp-scene w-full h-auto" viewBox="0 0 440 380" fill="none" xmlns="http://www.w3.org/2000/svg"
    role="img" aria-label="A litter picker clears three pieces of litter from the ground, dropping each one into a purple collection bag.">

    {{-- Flat poster disc behind the bag. cx + r must stay inside the 440-unit
         viewBox or the SVG viewport shears a flat edge off the disc's right. --}}
    <circle cx="326" cy="212" r="110" fill="currentColor" opacity="0.10" />

    {{-- Ground: a solid bar, not a soft ellipse --}}
    <rect x="14" y="302" width="412" height="7" fill="currentColor" opacity="0.85" />

    {{-- The purple bag, open-mouthed: this is the bag being filled, whereas
         components/purple-bag-art.blade.php is the same bag knotted shut and
         left at a collection point. The two share the palette, the logo mark and
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

            {{-- The logo, printed on the bag: the same disc-and-branch mark as
                 components/logo-mark.blade.php, scaled from its 40-unit box.
                 Take any change to the branch from there rather than redrawing
                 it. Only the ink is local: the disc takes the bag's own darkest
                 purple instead of the header's near-black and the branch the
                 palette's off-white instead of pure white, so the mark reads as
                 printed on the plastic rather than a black sticker punched
                 through it. The geometry must not diverge. --}}
            <g transform="translate(102 142) scale(1.4)">
                <circle cx="20" cy="20" r="20" fill="#551f4f" />
                <g stroke="#fdf2fb" stroke-linecap="round">
                    <path d="M20 33V9" stroke-width="3.4" />
                    <g stroke-width="3">
                        <path d="M20 26L11 18" />
                        <path d="M20 20L29 12" />
                    </g>
                </g>
            </g>
        </g>
    </g>

    {{-- Impact ticks, flicked off the bag mouth as each piece lands. Placed clear
         of the knot and its loose ends. --}}
    <g class="lp-impact" stroke="#e99edd" stroke-width="5" stroke-linecap="round" opacity="0">
        <path d="M294 182l-15-12" />
        <path d="M330 168v-16" />
        <path d="M366 182l15-12" />
    </g>

    {{-- The three pieces, in the order the grabber takes them: the can by the
         grabber's feet, the tub off to the left, then the wrapper to the right.
         All three sit *after* the bag in the DOM so a carried piece passes in
         front of it on the way in rather than disappearing behind it — at rest
         none of them overlaps the bag, so the static frame is unaffected.

         Each piece's ground centre must line up with where the arm parks for it
         (see the x offsets in lp-arm): 105, 55 and 166.

         Greyed rather than coloured, so the litter stays the drab thing being
         removed and the purple keeps the eye. The two greys are fixed, not
         currentColor: a mid slate and a pale one that both hold their contrast
         on the light page and the dark one. --}}
    <g class="lp-litter lp-litter-a">
        <rect x="92" y="284" width="26" height="18" fill="#64748b" />
        <rect x="92" y="284" width="26" height="6" fill="#cbd5e1" />
    </g>

    <g class="lp-litter lp-litter-b">
        <rect x="40" y="288" width="30" height="14" fill="#64748b" />
        <rect x="40" y="288" width="30" height="5" fill="#cbd5e1" />
    </g>

    <g class="lp-litter lp-litter-c">
        <path d="M150 302l6-13 14 4 10-7 3 16z" fill="#64748b" />
        <path d="M156 289l14 4 10-7 1 5-11 5-13-4z" fill="#cbd5e1" />
    </g>

    {{-- The picker. Drawn low enough in the viewBox that the grip stays on canvas
         when the arm lifts by 76px, and narrow enough that its full x travel
         (-50 to +230 from here) keeps the jaws inside the 440-unit viewBox. --}}
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
