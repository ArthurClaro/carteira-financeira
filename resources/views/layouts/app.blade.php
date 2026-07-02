<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="h-full bg-gray-50 text-gray-900 antialiased">
        <div class="min-h-full">
            @auth
                <nav class="border-b border-gray-200 bg-white">
                    <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="flex items-center gap-2 text-lg font-semibold text-indigo-600">
                            <span>💳</span> {{ config('app.name') }}
                        </a>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-gray-600">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-md px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-100">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>
            @endauth

            <main class="mx-auto max-w-4xl px-4 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
