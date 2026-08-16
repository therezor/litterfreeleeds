<x-layouts.app title="Choose your password"
    description="Your email address is confirmed. Choose a password to finish setting up your account.">

    <x-page-hero eyebrow="Last step" heading="Choose a password"
        subheading="Thanks {{ $name }} — your email address is confirmed and your Purple Bag Holder has been told you've joined. One last thing and your account is ready." />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="max-w-2xl">

                @if ($errors->any())
                    <div class="mb-8 border-l-4 border-red-600 bg-red-50 dark:bg-red-500/10 p-6" role="alert">
                        <p class="font-bold text-red-800 dark:text-red-300">
                            Please check the form — {{ $errors->count() }}
                            {{ Str::plural('detail', $errors->count()) }} needs your attention.
                        </p>
                    </div>
                @endif

                <form method="post" action="{{ route('join.password.store') }}" class="space-y-8">
                    @csrf

                    <x-form-field name="password" label="Your new password" type="password" required
                        autocomplete="new-password"
                        help="At least 8 characters. A few words strung together is easier to remember than something short and cryptic." />

                    <x-form-field name="password_confirmation" label="Type it again" type="password" required
                        autocomplete="new-password" />

                    <button type="submit"
                        class="group inline-flex items-center gap-3 px-8 py-4 bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 text-base font-bold uppercase tracking-wider shadow-hard dark:shadow-hard-light hover:shadow-hard-sm dark:hover:shadow-hard-light-sm hover:translate-x-[3px] hover:translate-y-[3px] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                        Save and finish
                        <x-heroicon-o-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                            aria-hidden="true" />
                    </button>
                </form>

                <p class="mt-10 pt-8 border-t-2 border-ink-950/12 dark:border-white/12 text-sm text-ink-950/60 dark:text-brand-100/60 leading-relaxed">
                    {{-- Said plainly, because the alternative is people worrying they
                         have missed a password we sent them. --}}
                    You don't need an account to go out and pick litter — it's where you can update your details and
                    see your local group. If you'd rather do this later, close this page and use
                    <strong>forgotten password</strong> on the <a href="{{ url('/app/login') }}"
                        class="font-bold text-brand-700 dark:text-brand-300 underline underline-offset-4">sign-in
                        page</a> whenever you want.
                </p>

            </div>
        </div>
    </section>

</x-layouts.app>
