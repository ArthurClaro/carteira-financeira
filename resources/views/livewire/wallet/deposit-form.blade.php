<div class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm transition hover:shadow-md">
    <h2 class="text-lg font-semibold">Depositar</h2>
    <p class="mb-4 text-sm text-gray-500">Adicione fundos à sua carteira.</p>

    @if ($success)
        <div class="animate-slide-in mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
            {{ $success }}
        </div>
    @endif

    <form wire:submit="deposit" class="space-y-3">
        <div>
            <label for="deposit-amount" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
            <input id="deposit-amount" type="number" step="0.01" min="0" inputmode="decimal"
                   wire:model="amount" placeholder="0,00"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 tabular-nums shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
            @error('amount') <p class="animate-slide-in mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="deposit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white shadow-sm transition-all hover:-translate-y-px hover:bg-emerald-500 hover:shadow active:translate-y-0 active:scale-[.98] disabled:opacity-60 disabled:hover:translate-y-0">
            <svg wire:loading wire:target="deposit" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
            </svg>
            <span wire:loading.remove wire:target="deposit">Depositar</span>
            <span wire:loading wire:target="deposit">Processando...</span>
        </button>
    </form>
</div>
