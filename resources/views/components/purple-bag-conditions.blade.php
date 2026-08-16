@props(['headingLevel' => 'h2'])

{{-- Transcribed from Leeds City Council's "Use of Leeds City Council Purple
     Litter Bags — Conditions of Use". Reproduced rather than summarised: it is
     LCC's document and volunteers are asked to follow it as written. Source PDF
     linked at the foot of this component. --}}

@php
    $heading = $headingLevel;
    $sub = $headingLevel === 'h2' ? 'h3' : 'h4';

    $dos = [
        'If working alone, always ensure that a family member or friend are aware of your whereabouts',
        'Always wear gloves and use litter pickers when litter picking',
        'Always wear reflective or light clothing, ideally a hi-vis vest',
        'Follow current restrictions and advice around preventing the spread of Coronavirus',
        'Carry a mobile phone if possible in case of emergency',
        'Only litter pick on safe to access public land',
        'Be aware of your health and safety and surroundings, only pick where it is safe to do so',
        'Ensure that you are wearing a strong pair of boots or shoes',
        'Wash your hands and carry hand sanitiser',
        'Ensure that children are accompanied and supervised by a known and responsible adult at all times',
        'Follow any appropriate guidance if you have Duke of Edinburgh volunteers working with you',
    ];

    $donts = [
        'Handle any needles or sharp objects',
        'Litter pick on or close to any busy or high speed roads, and never close to roads with speed limits over 40 mph. On lower speed roads don\'t litter pick unless there is a path or flat grass verge that allows you to pick a distance from the traffic',
        'Lift or pick large or heavy items',
        'Lift or collect flytipped waste or items where you don\'t know what the material may be (i.e. could it be asbestos?)',
        'Litter pick private land unless you have specific permission to',
        'Litter pick in the dark or in inclement weather',
        'Work in or near watercourses',
    ];

    $faqs = [
        'Who can use the purple bags?' =>
            'These bags are provided for free by Leeds City Council to Litter Free Leeds group organisers and bag holders for them to distribute to local volunteers as they feel appropriate. Anyone using the bags should read and adhere to the guidance set out in this document, with particular attention given to the safe working rules and where to leave bags for collection. All litter collected MUST be put in these distinctive purple bags. It is important that Leeds City Council can differentiate waste that has been left on the side of the road, and that these bags are only used by volunteer community litter pickers. Unless specifically told otherwise, the white LCC bags should not be used.',
        'How do I get some more purple bags?' =>
            'Purple bags can only be provided direct by LCC to those on the Litter Free Leeds list of group organisers and bag holders. They should email their requests to purplebags@leeds.gov.uk.',
        'Does Leeds City Council collect the bags once full?' =>
            'Yes, providing the litter pick was on public land. It is important that you do all you can to leave them next to a Leeds City Council litter bin. Please be aware though, the frequency of emptying of some of the bins does vary from several times a week to less than once a week depending on its location and how often it would normally fill up. If you cannot leave the full bags near a Leeds City Council litter bin, then you must leave them at a location where it is possible for a caged wagon to safely pull in. So NOT on a junction, or on the side of a busy main road, or a narrow country road. A bus stop or layby would be an obvious safer place to leave them. You must let the Council know exactly where you have left the bags by emailing purplebags@leeds.gov.uk. If the litter pick is a group activity and likely to produce several bags for collection, it would be helpful to forewarn the council so a collection can be planned. Please don\'t assume that because you have posted your achievements on the Litter Free Leeds Facebook page, the Council will be aware.',
        'Does Leeds City Council provide litter picking equipment?' =>
            'The Council\'s Cleaner Neighbourhoods Team have stocks of litter picking equipment available for short loan. They are looking at how further stocks could be purchased, including smaller grabbers for children, and then distributed through the group organisers and bag holders to either loan out or donate to local volunteers direct.',
        'What happens to the litter that is collected — is it recycled?' =>
            'The litter that is collected is taken to the waste transfer station at Kirkstall, where it is bulked up with the white bags of litter from the council litter bins and taken to AWM. Because of the amount of contamination, mainly dog-poo bags and their content, it is not possible to sort this waste for recycling. The bags are taken directly to the shredder and sent to make Refuse Derived Fuel. In order to recycle any litter found you would need to separate it into a bag that just contained the right items or materials, and put it in your green bin at home.',
        'Why is there a problem with litter picking private or non-council land?' =>
            'First and foremost because without permission you may be trespassing. Even though you think you are helping, the landowner may not agree with you — for example if it was farmland with livestock or crops in. Also, the council may already be taking enforcement action requiring the land to be cleared of litter. As a group or individual you could choose to litter pick private land, or land that belongs to a business, school or the Canal and River Trust. But providing you have successfully contacted the owner of the land to gain permission, they should then dispose of the waste at their cost, not the Leeds council tax payers\'. This may also encourage them to take more responsibility themselves in the future.',
        'What about larger, non-litter items I come across?' =>
            'Many group litter picks end up finding items that have somehow found themselves in the undergrowth at the road side — such as wheel hubs, broken signs, broken bumpers and so on. If they don\'t fit in the purple bag then they can be neatly left by the side of them for collection, but please forewarn the council crews, particularly if there is a lot, by emailing purplebags@leeds.gov.uk. Flytips should be reported through the Council\'s online reporting page at leeds.gov.uk/flytipping. The time taken to remove flytips will depend on the time to gather evidence first, whether it is actually on council land, and of course the competing priorities of the crews.',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'max-w-3xl']) }}>

    <div class="border-l-4 border-brand-600 bg-brand-50 dark:bg-brand-500/10 p-6 mb-10">
        <p class="text-sm text-ink-950/80 dark:text-brand-100/80 leading-relaxed">
            All litter you collect <strong class="font-bold text-ink-950 dark:text-white">must</strong> go in the
            distinctive purple bags. That is how Leeds City Council tells volunteer litter picking apart from waste
            dumped at the roadside. Unless you are told otherwise, don't use the white LCC bags.
        </p>
    </div>

    <{{ $heading }} class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-6">
        Safe working rules
    </{{ $heading }}>

    <div class="grid sm:grid-cols-2 gap-8 mb-12">
        <div>
            <{{ $sub }}
                class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.22em] text-green-700 dark:text-green-400 mb-4">
                <x-heroicon-s-check-circle class="w-5 h-5" aria-hidden="true" />
                Do
            </{{ $sub }}>
            <ul class="space-y-3">
                @foreach ($dos as $item)
                    <li class="flex gap-3 text-sm text-ink-950/75 dark:text-brand-100/75 leading-relaxed">
                        <span class="mt-2 shrink-0 w-1.5 h-1.5 bg-green-600 dark:bg-green-400"
                            aria-hidden="true"></span>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <{{ $sub }}
                class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.22em] text-red-700 dark:text-red-400 mb-4">
                <x-heroicon-s-x-circle class="w-5 h-5" aria-hidden="true" />
                Don't
            </{{ $sub }}>
            <ul class="space-y-3">
                @foreach ($donts as $item)
                    <li class="flex gap-3 text-sm text-ink-950/75 dark:text-brand-100/75 leading-relaxed">
                        <span class="mt-2 shrink-0 w-1.5 h-1.5 bg-red-600 dark:bg-red-400" aria-hidden="true"></span>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="divide-y divide-ink-950/12 dark:divide-white/12 border-t-2 border-ink-950/12 dark:border-white/12">
        @foreach ($faqs as $question => $answer)
            <div class="py-8">
                <{{ $heading }} class="display text-xl sm:text-2xl text-ink-950 dark:text-white mb-4">
                    {{ $question }}
                </{{ $heading }}>
                <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed">{{ $answer }}</p>
            </div>
        @endforeach
    </div>

    <p class="mt-10 text-sm text-ink-950/55 dark:text-brand-100/55 leading-relaxed">
        Reproduced from Leeds City Council's <em>Use of Leeds City Council Purple Litter Bags — Conditions of
            Use</em>. Questions about bags and collections go to
        <a href="mailto:purplebags@leeds.gov.uk"
            class="font-bold text-brand-700 dark:text-brand-300 underline underline-offset-4">purplebags@leeds.gov.uk</a>.
    </p>
</div>
