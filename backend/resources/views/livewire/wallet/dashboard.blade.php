<div>
    <h1 class="text-2xl font-bold">Olá, {{ auth()->user()->name }}</h1>

    <div class="mt-6 rounded-xl border border-gray-200 bg-gradient-to-br from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
        <p class="text-sm text-indigo-100">Saldo atual</p>
        <p class="mt-1 text-4xl font-bold tracking-tight">{{ $wallet->balance()->format() }}</p>
    </div>
</div>
