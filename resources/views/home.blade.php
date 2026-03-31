<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased font-sans scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Litter Free Leeds') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ 
        mobileMenuOpen: false, 
        darkMode: document.documentElement.classList.contains('dark')
    }"
    x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark') })"
    class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col font-sans transition-colors duration-300">

    {{-- Navbar (Filament Style) --}}
    <header
        class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl sticky top-0 z-50 ring-1 ring-gray-950/5 dark:ring-white/10 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-3">
                    {{-- Logo / Brand --}}
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-bold text-lg shadow-sm ring-1 ring-black/10 dark:ring-white/10">
                        L
                    </div>
                    <span
                        class="font-extrabold tracking-tight text-gray-900 dark:text-white text-sm sm:text-xl leading-tight">
                        Litter Free<br class="sm:hidden" /> Leeds
                    </span>
                </div>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex space-x-1">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 transition-colors">Home</a>
                    <a href="{{ url('/upcoming-picks') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Upcoming
                        Picks</a>
                    <a href="{{ url('/about') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">About</a>
                    <a href="{{ url('/contact-us') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Contact
                        Us</a>
                </nav>

                {{-- Auth / CTA --}}
                <div class="flex items-center gap-4">
                    {{-- Light/Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2 transition">
                        {{-- Dark icon (moon) --}}
                        <svg x-cloak x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        {{-- Light icon (sun) --}}
                        <svg x-cloak x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    @auth
                        <a href="{{ url('/app') }}"
                            class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-xl text-gray-700 bg-white ring-1 ring-gray-950/10 hover:bg-gray-50 dark:text-gray-200 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 transition-all focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950 shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ url('/app/login') }}"
                            class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-xl text-gray-700 bg-white ring-1 ring-gray-950/10 hover:bg-gray-50 dark:text-gray-200 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 transition-all focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950 shadow-sm">
                            Log In
                        </a>
                    @endauth

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                        class="md:hidden text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Dropdown --}}
        <div x-cloak x-show="mobileMenuOpen" x-transition
            class="md:hidden border-t border-gray-200 dark:border-white/10 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl absolute w-full left-0 shadow-lg transition-all">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ url('/') }}"
                    class="block px-3 py-2 rounded-lg text-base font-semibold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 transition-colors">Home</a>
                <a href="{{ url('/upcoming-picks') }}"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Upcoming
                    Picks</a>
                <a href="{{ url('/about') }}"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">About</a>
                <a href="{{ url('/contact-us') }}"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Contact
                    Us</a>

                <div class="border-t border-gray-200 dark:border-white/10 mt-2 pt-2 sm:hidden">
                    @auth
                        <a href="{{ url('/app') }}"
                            class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ url('/app/login') }}"
                            class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors">Log
                            In</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <main class="flex-grow">
        <div class="relative overflow-hidden bg-white dark:bg-gray-950 transition-colors duration-300">
            {{-- Background Gradients --}}
            <div class="absolute inset-0 z-0">
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-purple-100/50 via-white to-white dark:from-purple-900/20 dark:via-gray-950 dark:to-gray-950 transition-colors duration-300">
                </div>
                {{-- Dynamic blur blobs for depth --}}
                <div
                    class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] bg-purple-300/30 dark:bg-purple-800/20 rounded-full blur-3xl opacity-50 mix-blend-multiply dark:mix-blend-lighten animate-pulse">
                </div>
                <div
                    class="absolute top-[20%] -left-[10%] w-[40%] h-[40%] bg-fuchsia-300/20 dark:bg-fuchsia-900/10 rounded-full blur-3xl opacity-50 mix-blend-multiply dark:mix-blend-lighten animate-pulse delay-1000">
                </div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 xl:py-40">
                <div class="flex flex-col items-center text-center max-w-4xl mx-auto">
                    {{-- Badge --}}
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-300 text-sm font-semibold mb-8 ring-1 ring-purple-600/20 dark:ring-purple-500/30 backdrop-blur-sm shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                        </span>
                        Leeds Goes Purple
                    </div>

                    <h1
                        class="text-5xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-6xl md:text-7xl mb-8 leading-tight">
                        We can truly make a <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-fuchsia-500 dark:from-purple-400 dark:to-fuchsia-400 drop-shadow-sm">difference</span>
                    </h1>

                    <p
                        class="mt-2 text-lg text-gray-600 dark:text-gray-400 sm:text-xl max-w-2xl font-medium leading-relaxed">
                        We have over 50 groups who go out regularly litter picking throughout the whole of the City.
                        Join the thousands making Leeds cleaner and greener.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center w-full sm:w-auto">
                        <a href="{{ url('app/register') }}"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 border border-transparent text-lg font-semibold rounded-xl text-white bg-purple-600 hover:bg-purple-500 hover:shadow-xl hover:shadow-purple-600/20 dark:bg-purple-500 dark:hover:bg-purple-400 dark:hover:shadow-purple-900/30 transition-all transform hover:-translate-y-0.5">
                            Join Us Now
                        </a>
                        <a href="{{ url('/upcoming-picks') }}"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 border border-gray-200 dark:border-white/10 text-lg font-semibold rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10 shadow-sm transition-all transform hover:-translate-y-0.5 ring-1 ring-transparent focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                            Upcoming Picks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Features/Info Section (Filament Style Cards) --}}
        <div
            class="bg-gray-50 dark:bg-gray-900/50 py-24 transition-colors duration-300 relative border-y border-gray-200 dark:border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Our Mission
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Everything we do is volunteer-driven to
                        preserve the beauty of our neighborhoods.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    {{-- Card 1 --}}
                    <div
                        class="group bg-white dark:bg-gray-900 rounded-3xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-8 hover:shadow-xl hover:shadow-purple-500/5 dark:hover:shadow-purple-900/20 transition-all duration-300 relative overflow-hidden flex flex-col">
                        <div
                            class="absolute top-0 right-0 p-6 opacity-0 group-hover:opacity-10 transition-opacity duration-300">
                            <svg class="w-24 h-24 text-purple-600 dark:text-purple-400 transform translate-x-4 -translate-y-4"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div
                            class="w-14 h-14 bg-purple-50 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-6 ring-1 ring-purple-900/10 dark:ring-purple-500/20 shadow-sm relative z-10 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 relative z-10">Protecting
                            Wildlife</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-medium flex-grow relative z-10">
                            Wildlife so often become the victims of littered items. Let's help protect them in their
                            natural habitats. Every bag counts.
                        </p>
                    </div>

                    {{-- Card 2 --}}
                    <div
                        class="group bg-white dark:bg-gray-900 rounded-3xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-8 hover:shadow-xl hover:shadow-purple-500/5 dark:hover:shadow-purple-900/20 transition-all duration-300 relative overflow-hidden flex flex-col md:-translate-y-4">
                        <div
                            class="absolute top-0 right-0 p-6 opacity-0 group-hover:opacity-10 transition-opacity duration-300">
                            <svg class="w-24 h-24 text-purple-600 dark:text-purple-400 transform translate-x-4 -translate-y-4"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div
                            class="w-14 h-14 bg-purple-50 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-6 ring-1 ring-purple-900/10 dark:ring-purple-500/20 shadow-sm relative z-10 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 relative z-10">Community Driven
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-medium flex-grow relative z-10">
                            Active volunteers for years, steadily growing to thousands of people cleaning up their
                            neighborhoods.
                        </p>
                    </div>

                    {{-- Card 3 --}}
                    <div
                        class="group bg-white dark:bg-gray-900 rounded-3xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-8 hover:shadow-xl hover:shadow-purple-500/5 dark:hover:shadow-purple-900/20 transition-all duration-300 relative overflow-hidden flex flex-col">
                        <div
                            class="absolute top-0 right-0 p-6 opacity-0 group-hover:opacity-10 transition-opacity duration-300">
                            <svg class="w-24 h-24 text-purple-600 dark:text-purple-400 transform translate-x-4 -translate-y-4"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <div
                            class="w-14 h-14 bg-purple-50 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-6 ring-1 ring-purple-900/10 dark:ring-purple-500/20 shadow-sm relative z-10 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 relative z-10">The Purple Bag
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-medium flex-grow relative z-10">
                            Wherever you see a purple bag, it has been collected by a volunteer. We provide bags and
                            help you get started.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Call to Action Banner --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div
                class="bg-gradient-to-br from-purple-700 to-purple-900 dark:from-purple-800 dark:to-gray-900 rounded-3xl shadow-2xl p-10 md:p-16 text-center ring-1 ring-white/10 relative overflow-hidden">
                {{-- Decorative pattern --}}
                <div class="absolute inset-0 opacity-10"
                    style="background-image: radial-gradient(#ffffff 2px, transparent 2px); background-size: 30px 30px;">
                </div>

                <div class="relative z-10 max-w-3xl mx-auto">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl mb-6">
                        Ready to make a difference?
                    </h2>
                    <p class="text-lg md:text-xl text-purple-100 font-medium mb-10 text-balance">
                        Whatever time you have available, however small, will be very much appreciated. Every bag counts
                        towards a cleaner Leeds.
                    </p>
                    <a href="{{ url('/contact-us') }}"
                        class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl text-purple-900 bg-white hover:bg-purple-50 shadow-xl transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-purple-700 focus:ring-white">
                        Start Volunteering Today
                    </a>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer
        class="bg-white dark:bg-gray-950 border-t border-gray-200 dark:border-white/10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col items-center justify-between gap-8 md:flex-row md:items-center">

                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        L</div>
                    <span class="text-gray-900 dark:text-white font-bold text-lg tracking-tight">Litter Free
                        Leeds</span>
                </div>

                {{-- Links --}}
                <nav
                    class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                    <a href="{{ url('/about') }}"
                        class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">About</a>
                    <a href="{{ url('/privacy-policy') }}"
                        class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Privacy Policy</a>
                    <a href="{{ url('/site-map') }}"
                        class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Site Map</a>
                </nav>

            </div>

            <div
                class="mt-8 border-t border-gray-100 dark:border-white/5 pt-8 flex items-center justify-center md:justify-between flex-wrap gap-4">
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    &copy; {{ date('Y') }} Litter Free Leeds. All Rights Reserved.
                </p>
                <p class="text-sm text-gray-400 dark:text-gray-500 flex items-center gap-1">
                    Made with <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd"></path>
                    </svg> by Volunteers
                </p>
            </div>
        </div>
    </footer>

</body>

</html>