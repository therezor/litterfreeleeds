@props(['class' => 'w-9 h-9'])

{{--
    Litter Free Leeds has a real logo that this repo holds no copy of. Drop it in
    at public/images/logo.svg (or .png) and it is picked up automatically here,
    in the header and the footer; the drawn mark is the fallback until then.

    The size is applied here and only here — x-logo-mark sets no default, so the
    caller's w-*/h-* can't collide with one.
--}}
@php
    $logo = collect(['images/logo.svg', 'images/logo.png'])
        ->first(fn (string $path): bool => file_exists(public_path($path)));
@endphp

@if ($logo)
    <img src="{{ asset($logo) }}" alt="{{ config('app.name', 'Litter Free Leeds') }}"
        class="{{ $class }} object-contain shrink-0">
@else
    <x-logo-mark class="{{ $class }} shrink-0" />
@endif
