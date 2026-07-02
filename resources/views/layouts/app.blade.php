<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="h-full bg-brand-50/40 text-gray-900 antialiased">
        <div class="min-h-full">
            @auth
                <nav class="sticky top-0 z-10 border-b border-brand-100 bg-white/85 backdrop-blur">
                    <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="flex items-center gap-2 text-lg font-semibold text-brand-800 transition hover:text-brand-600">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v8.5A2.25 2.25 0 0 1 18.75 19H5.25A2.25 2.25 0 0 1 3 16.75v-8.5Z"/>
                                <path stroke-linecap="round" d="M3 10h18"/>
                                <circle cx="17" cy="14.5" r="1.4" fill="currentColor" stroke="none"/>
                            </svg>
                            {{ config('app.name') }}
                        </a>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-800"
                                  aria-hidden="true">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden text-gray-600 sm:inline">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-md px-3 py-1.5 font-medium text-gray-700 transition hover:bg-brand-50 hover:text-brand-800">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>
            @endauth

            <main class="mx-auto max-w-4xl px-4 py-8">
                @if (session('status'))
                    <div class="animate-slide-in mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
