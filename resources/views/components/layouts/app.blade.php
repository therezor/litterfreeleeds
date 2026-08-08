@props([
    'title' => null,
    'description' => 'Litter Free Leeds — over 50 volunteer groups litter picking across the whole of the City of Leeds.',
    'active' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased font-sans scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' — ' . config('app.name', 'Litter Free Leeds') : config('app.name', 'Litter Free Leeds') }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#0b0a0c">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{-- Inter, self-hosted by Filament (family: 'Inter Variable'); Oswald, self-hosted --}}
    <link rel="stylesheet" href="{{ asset('fonts/filament/filament/inter/index.css') }}">
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/oswald/oswald-latin.woff2') }}" crossorigin>
    <link rel="stylesheet" href="{{ asset('fonts/oswald/index.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{
        mobileMenuOpen: false,
        darkMode: document.documentElement.classList.contains('dark')
    }"
    x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark') })"
    class="bg-white text-gray-900 dark:bg-ink-950 dark:text-gray-100 min-h-screen flex flex-col font-sans transition-colors duration-300">

    <x-site-header :active="$active" />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-site-footer />

</body>

</html>
