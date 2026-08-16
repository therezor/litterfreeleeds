<x-layouts.app title="You're all set" description="Your account is ready and your Purple Bag Holder has been told you've joined.">

    {{-- Reachable on its own, so the copy has to make sense without the flash:
         the password line is the extra, not the substance. --}}
    <x-page-hero eyebrow="All set" heading="You're all set"
        :subheading="session('passwordSet')
            ? 'Email confirmed and password saved — you are signed in. Your local Purple Bag Holder has been told you have joined, and will be in touch soon.'
            : 'Your local Purple Bag Holder has been told you have joined. They will be in touch soon.'" />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="max-w-2xl">
                <p class="text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                    While you wait to hear from them, have a look at the picks happening near you — or read the purple
                    bag conditions of use so you are ready to go the moment your bags arrive.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <x-action-button :href="url('/upcoming-picks')" variant="primary">
                        Find a pick near you
                    </x-action-button>
                    <x-action-button :href="route('purple-bag-conditions')" variant="outline">
                        Conditions of use
                    </x-action-button>
                    {{-- Most people land here from an email on a device with no
                         session, so this is a sign-in prompt rather than a
                         dashboard link. --}}
                    <x-action-button :href="auth()->check() ? url('/app') : url('/app/login')" variant="outline">
                        {{ auth()->check() ? 'Go to your dashboard' : 'Sign in to your account' }}
                    </x-action-button>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
