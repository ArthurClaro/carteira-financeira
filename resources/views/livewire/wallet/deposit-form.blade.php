<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-semibold">Depositar</h2>
    <p class="mb-4 text-sm text-gray-500">Adicione fundos à sua carteira.</p>

    @if ($success)
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800">
            {{ $success }}
        </div>
    @endif

    <form wire:submit="deposit" class="space-y-3">
        <div>
            <label for="deposit-amount" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
            <input id="deposit-amount" type="number" step="0.01" min="0" inputmode="decimal"
                   wire:model="amount" placeholder="0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="deposit"
                class="w-full rounded-md bg-emerald-600 px-4 py-2 font-medium text-white transition hover:bg-emerald-500 disabled:opacity-60">
            <span wire:loading.remove wire:target="deposit">Depositar</span>
            <span wire:loading wire:target="deposit">Processando...</span>
        </button>
    </form>
</div>
