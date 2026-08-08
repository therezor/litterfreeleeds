@props(['value' => null, 'failed' => false, 'sorted' => false])

{{-- Plain GET form: no JS, shareable URL, and it degrades to the normal
     chronological listing when the postcode means nothing to us. --}}
<div class="border-b-2 border-ink-950/12 dark:border-white/12 pb-8">
    <form method="get" action="{{ url('/upcoming-picks') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[14rem]">
            <label for="postcode"
                class="block mb-3 text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
                Find picks near you
            </label>
            <input id="postcode" name="postcode" type="text" inputmode="text" autocomplete="postal-code"
                value="{{ $value }}" placeholder="LS1 1UR" aria-describedby="postcode-help"
                class="w-full border-2 border-ink-950 dark:border-white/40 bg-transparent px-4 py-3 text-ink-950 dark:text-white placeholder:text-ink-950/35 dark:placeholder:text-brand-100/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600">
        </div>

        <button type="submit"
            class="group inline-flex items-center gap-3 px-7 py-3.5 text-sm sm:text-base font-bold uppercase tracking-wider bg-brand-700 dark:bg-brand-400 text-white dark:text-brand-950 shadow-hard dark:shadow-hard-light hover:shadow-hard-sm dark:hover:shadow-hard-light-sm hover:translate-x-[3px] hover:translate-y-[3px] transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-600">
            Search
            <x-heroicon-o-magnifying-glass class="w-5 h-5" aria-hidden="true" />
        </button>
    </form>

    <p id="postcode-help" class="mt-4 text-sm text-ink-950/60 dark:text-brand-100/60">
        @if ($failed)
            We don't recognise <strong class="text-ink-950 dark:text-white">{{ $value }}</strong>. Here are all our
            upcoming picks instead.
        @elseif ($sorted)
            Sorted by distance from <strong class="text-ink-950 dark:text-white">{{ $value }}</strong>.
            <a href="{{ url('/upcoming-picks') }}"
                class="underline underline-offset-4 hover:text-brand-700 dark:hover:text-brand-300">Show all by
                date</a>.
        @else
            Enter a postcode to sort the picks below by how close they are to you.
        @endif
    </p>
</div>
