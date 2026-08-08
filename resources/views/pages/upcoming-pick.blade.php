@php
    // excerpt, not description: excerpt is required and always present, and it
    // is written to be a summary.
    $metaDescription = Str::limit($pick->excerpt, 155);

    $details = [
        ['icon' => 'calendar-days', 'label' => 'Date', 'value' => $pick->date->translatedFormat('l j F Y')],
        ['icon' => 'clock', 'label' => 'Time', 'value' => $pick->time_range],
        ['icon' => 'map-pin', 'label' => 'Meeting point', 'value' => $pick->location . ', ' . $pick->formatted_postcode],
        ['icon' => 'user', 'label' => 'Organised by', 'value' => $pick->responsibleUser->name],
    ];
@endphp

<x-layouts.app :title="$pick->name" active="upcoming-picks" :description="$metaDescription">

    <x-page-hero :eyebrow="$pick->date->translatedFormat('l j F Y')" :heading="$pick->name" :subheading="$pick->excerpt" />

    <section class="bg-white dark:bg-ink-900">
        <div class="max-w-[92rem] mx-auto px-5 sm:px-8 py-20 lg:py-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

                <div class="lg:col-span-4">
                    <dl class="border-t-2 border-ink-950/12 dark:border-white/12">
                        @foreach ($details as $detail)
                            <div class="border-b-2 border-ink-950/12 dark:border-white/12 py-5">
                                <dt
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
                                    <x-dynamic-component :component="'heroicon-o-' . $detail['icon']" class="w-4 h-4 shrink-0"
                                        aria-hidden="true" />
                                    {{ $detail['label'] }}
                                </dt>
                                <dd class="mt-2 text-lg text-ink-950 dark:text-white">{{ $detail['value'] }}</dd>
                            </div>
                        @endforeach

                        @if ($pick->place_label)
                            <div class="border-b-2 border-ink-950/12 dark:border-white/12 py-5">
                                <dt class="text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
                                    Area
                                </dt>
                                <dd class="mt-2 text-lg text-ink-950 dark:text-white">{{ $pick->place_label }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-9 flex flex-wrap gap-4">
                        <x-action-button :href="$pick->directions_url" target="_blank" rel="noopener noreferrer">
                            Get directions
                        </x-action-button>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <x-pick-map :pick="$pick" class="mb-12" />

                    <div class="prose-lg max-w-none space-y-6 text-lg text-ink-950/70 dark:text-brand-100/70 leading-relaxed">
                        @forelse (preg_split('/\R{2,}/', trim((string) ($pick->description ?: $pick->excerpt))) as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @empty
                            <p>{{ $pick->excerpt }}</p>
                        @endforelse
                    </div>

                    <div class="mt-12 border-t-2 border-ink-950/12 dark:border-white/12 pt-9">
                        <p class="text-ink-950/70 dark:text-brand-100/70 leading-relaxed max-w-2xl">
                            Bring gloves if you have them — we can provide bags and try to provide other equipment where
                            we can. Whatever time you have available, however small, will be very much appreciated.
                        </p>
                        <div class="mt-9">
                            <x-action-button :href="url('/upcoming-picks')" variant="outline">
                                All upcoming picks
                            </x-action-button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
