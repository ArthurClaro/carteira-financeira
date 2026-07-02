<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold">Olá, {{ auth()->user()->name }}</h1>
        <div class="mt-4 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
            <p class="text-sm text-indigo-100">Saldo atual</p>
            <p class="mt-1 text-4xl font-bold tracking-tight">{{ $wallet->balance()->format() }}</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <livewire:wallet.deposit-form />
        <livewire:wallet.transfer-form />
    </div>

    <livewire:wallet.transaction-history />
</div>
