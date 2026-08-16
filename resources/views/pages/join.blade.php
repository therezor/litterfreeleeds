<x-layouts.app title="Join us" description="Sign up as a Litter Free Leeds volunteer and we'll put you in touch with your local Purple Bag Holder.">

    <x-page-hero eyebrow="Volunteer" heading="Join the pickers"
        subheading="Tell us where you are and we'll put you in touch with your nearest Purple Bag Holder — a local volunteer who'll sort you out with bags." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                <div class="lg:col-span-4">
                    <h2 class="display text-2xl sm:text-3xl text-ink-950 dark:text-white mb-6">How it works</h2>

                    <ol class="space-y-6">
                        @foreach ([
        'Sign up' => 'Name, email and postcode. No password to think up — you choose one after confirming your email.',
        'Get matched' => 'We find the Purple Bag Holder closest to you and let them know you have joined.',
        'Start picking' => 'They get in touch, hand over purple bags, and you are away.',
    ] as $step => $detail)
                            <li class="flex gap-4">
                                <span
                                    class="shrink-0 w-9 h-9 flex items-center justify-center bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 font-bold tag-corner">{{ $loop->iteration }}</span>
                                <div>
                                    <h3 class="font-bold text-ink-950 dark:text-white">{{ $step }}</h3>
                                    <p class="mt-1 text-sm text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                                        {{ $detail }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="lg:col-span-8 max-w-2xl">
                    @if ($errors->any())
                        <div class="mb-8 border-l-4 border-red-600 bg-red-50 dark:bg-red-500/10 p-6"
                            role="alert">
                            <p class="font-bold text-red-800 dark:text-red-300">
                                Please check the form — {{ $errors->count() }}
                                {{ Str::plural('detail', $errors->count()) }} needs your attention.
                            </p>
                        </div>
                    @endif

                    <form method="post" action="{{ route('join.store') }}" class="space-y-8">
                        @csrf

                        <x-form-field name="name" label="Your name" required autocomplete="name"
                            placeholder="Alex Fletcher" />

                        <x-form-field name="email" label="Email address" type="email" required
                            autocomplete="email" placeholder="you@example.com"
                            help="We'll send your welcome email here, and your Purple Bag Holder will use it to get in touch." />

                        <x-form-field name="postcode" label="Your postcode" required
                            autocomplete="postal-code" placeholder="LS1 1UR"
                            help="Used only to find the Purple Bag Holder nearest to you." />

                        {{-- No password field, by design: choosing one is its own
                             step, after the email address is confirmed. See
                             App\Http\Controllers\SetPasswordController. --}}

                        <div class="border-t-2 border-ink-950/12 dark:border-white/12 pt-8">
                            <label for="terms" class="flex gap-4 cursor-pointer">
                                {{-- Deliberately no @checked(old('terms')): every
                                     other field repopulates, but consent is not a
                                     preference to remember. If the form comes back
                                     with errors, agreeing has to be a fresh,
                                     deliberate act rather than something already
                                     ticked on the visitor's behalf. --}}
                                <input id="terms" name="terms" type="checkbox" value="1"
                                    @if ($errors->has('terms')) aria-invalid="true" aria-describedby="terms-error" @endif
                                    class="mt-1 shrink-0 w-5 h-5 border-2 border-ink-950 dark:border-white/40 text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
                                <span class="text-sm text-ink-950/80 dark:text-brand-100/80 leading-relaxed">
                                    I agree that Litter Free Leeds can store my name, email address and postcode, and
                                    share them with my nearest Purple Bag Holder so they can contact me about litter
                                    picking. I have read the
                                    <a href="{{ url('/privacy-policy') }}"
                                        class="font-bold text-brand-700 dark:text-brand-300 underline underline-offset-4">privacy
                                        policy</a>.
                                </span>
                            </label>

                            @error('terms')
                                <p id="terms-error" class="mt-3 text-sm font-bold text-red-700 dark:text-red-400">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="group inline-flex items-center gap-3 px-8 py-4 bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 text-base font-bold uppercase tracking-wider shadow-hard dark:shadow-hard-light hover:shadow-hard-sm dark:hover:shadow-hard-light-sm hover:translate-x-[3px] hover:translate-y-[3px] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                            Sign me up
                            <x-heroicon-o-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                                aria-hidden="true" />
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
