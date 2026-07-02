<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-semibold">Transferir</h2>
    <p class="mb-4 text-sm text-gray-500">Envie fundos para outro usuário.</p>

    @if ($success)
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800">
            {{ $success }}
        </div>
    @endif

    <form wire:submit="transfer" class="space-y-3">
        <div>
            <label for="recipient" class="block text-sm font-medium text-gray-700">E-mail do destinatário</label>
            <input id="recipient" type="email" wire:model="recipient_email" placeholder="destinatario@exemplo.com"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('recipient_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="transfer-amount" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
            <input id="transfer-amount" type="number" step="0.01" min="0" inputmode="decimal"
                   wire:model="amount" placeholder="0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="transfer"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-500 disabled:opacity-60">
            <span wire:loading.remove wire:target="transfer">Transferir</span>
            <span wire:loading wire:target="transfer">Processando...</span>
        </button>
    </form>
</div>
