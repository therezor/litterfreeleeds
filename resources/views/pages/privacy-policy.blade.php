<x-layouts.app title="Privacy Policy"
    description="How Litter Free Leeds handles the information you share with us.">

    <x-page-hero eyebrow="Legal" heading="Privacy Policy"
        subheading="How we handle the information you share with us." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                <div class="lg:col-span-4">
                    <div class="border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-500/10 p-6 flex gap-3">
                        <x-heroicon-o-exclamation-triangle
                            class="w-6 h-6 shrink-0 text-amber-700 dark:text-amber-300" />
                        <p class="text-sm text-amber-900 dark:text-amber-200 leading-relaxed">
                            <strong class="font-bold">Draft.</strong> This describes what the site actually does with
                            your details, but it has not yet been reviewed by anyone legally qualified.
                        </p>
                    </div>

                    <div class="mt-8 border-2 border-ink-950 dark:border-white/25 p-6">
                        <h2 class="text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300 mb-4">
                            In short</h2>
                        <p class="text-sm text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                            We collect your name, email address and postcode so that a volunteer near you can get in
                            touch and hand you some purple bags. That is the whole purpose. We do not sell your
                            details, and you can ask us to delete them at any time.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-8 max-w-3xl divide-y divide-ink-950/12 dark:divide-white/12">
                    @foreach ([
        'What we collect' =>
            'When you sign up as a volunteer we store your name, your email address and your postcode. You choose a password once you have confirmed your email address, and we store only an encrypted version of it that nobody — including us — can read back. We also record the date you agreed to this policy. If you go on to organise or help run community picks, we store the details of those picks alongside your account.',
        'Why we collect your postcode' =>
            'Only to work out which Purple Bag Holder is closest to you. We convert your postcode to an approximate location using the Office for National Statistics Postcode Directory, which gives the centre point of a postcode rather than a specific address. We never ask for, and never store, your street address.',
        'Who we share it with' =>
            'Your name, email address and postcode are shared with the one Purple Bag Holder we match you to, so they can contact you and arrange to hand over bags. Purple Bag Holders and Group Organisers are volunteers, not council staff. Nobody else sees your details, we do not sell them, and we do not pass them to advertisers.',
        'What Leeds City Council sees' =>
            'Leeds City Council provides the purple bags and collects them once full. We do not pass your personal details to the Council as part of that. If you email the Council directly — for example to tell them where you have left full bags — that correspondence is between you and them.',
        'How we use your email address' =>
            'To send you a welcome email with your onboarding instructions, to confirm your email address is real, and for occasional messages about litter picking in your area. We do not use it for marketing from anyone else.',
        'How long we keep it' =>
            'For as long as you are an active volunteer. If you ask us to delete your account we will remove your personal details. Where you have been the named organiser of a past community pick, we may keep a record of that pick without your contact details attached.',
        'Your rights' =>
            'You can ask us what we hold about you, ask us to correct it, ask us to delete it, or withdraw your consent to us processing it at any time. Withdrawing consent means we can no longer match you to a bag holder, so it effectively closes your volunteer account.',
        'Cookies' =>
            'We set a session cookie to keep you logged in and a security cookie to protect forms against cross-site request forgery. We do not use advertising or third-party tracking cookies.',
    ] as $heading => $body)
                        <div class="py-8 first:pt-0">
                            <h2 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-4">
                                {{ $heading }}</h2>
                            <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed">{{ $body }}</p>
                        </div>
                    @endforeach

                    <div class="py-8">
                        <h2 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-4">Getting in touch
                        </h2>
                        <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                            To ask what we hold about you, to have it corrected, or to have it removed, please
                            <a href="{{ url('/contact-us') }}"
                                class="font-bold text-brand-700 dark:text-brand-300 underline underline-offset-4">contact
                                us</a>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
