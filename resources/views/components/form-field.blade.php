@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'required' => false,
    'autocomplete' => null,
    'placeholder' => null,
])

@php
    // The field is described by its help text, its error, or both — screen
    // readers need the ids to exist only when the elements do.
    $describedBy = collect([
        $help ? $name . '-help' : null,
        $errors->has($name) ? $name . '-error' : null,
    ])->filter()->implode(' ');
@endphp

<div>
    <label for="{{ $name }}"
        class="block mb-3 text-xs font-bold uppercase tracking-[0.22em] text-brand-700 dark:text-brand-300">
        {{ $label }}
        @unless ($required)
            <span class="font-normal normal-case tracking-normal text-ink-950/50 dark:text-brand-100/50">(optional)</span>
        @endunless
    </label>

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ old($name, $value) }}" @if ($required) required @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($errors->has($name)) aria-invalid="true" @endif
        {{ $attributes->merge([
            'class' =>
                'w-full border-2 bg-transparent px-4 py-3 text-ink-950 dark:text-white placeholder:text-ink-950/35 dark:placeholder:text-brand-100/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 ' .
                ($errors->has($name)
                    ? 'border-red-600 dark:border-red-400'
                    : 'border-ink-950 dark:border-white/40'),
        ]) }}>

    @if ($help)
        <p id="{{ $name }}-help" class="mt-2 text-sm text-ink-950/60 dark:text-brand-100/60">{{ $help }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-2 text-sm font-bold text-red-700 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
