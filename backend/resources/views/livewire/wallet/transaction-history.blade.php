<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-lg font-semibold">Extrato</h2>

    @if ($flash)
        <div class="mb-4 rounded-md border px-3 py-2 text-sm {{ $flashIsError ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' }}">
            {{ $flash }}
        </div>
    @endif

    @if ($transactions->isEmpty())
        <p class="py-8 text-center text-sm text-gray-500">Nenhuma transação ainda.</p>
    @else
        <div class="divide-y divide-gray-100">
            @foreach ($transactions as $tx)
                @php
                    $incoming = $tx->to_wallet_id === $walletId;
                    $canReverse = $tx->status === \App\Enums\TransactionStatus::Completed
                        && ! $tx->isReversal()
                        && auth()->user()->can('reverse', $tx);
                @endphp
                <div class="flex items-center justify-between gap-4 py-3" wire:key="tx-{{ $tx->id }}">
                    <div>
                        <p class="font-medium text-gray-900">{{ $tx->type->label() }}</p>
                        <p class="text-xs text-gray-500">{{ $tx->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($tx->status === \App\Enums\TransactionStatus::Reversed)
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">Estornada</span>
                        @endif

                        <span class="font-semibold {{ $incoming ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $incoming ? '+' : '−' }} {{ $tx->amount()->format() }}
                        </span>

                        @if ($canReverse)
                            <button type="button"
                                    wire:click="reverse('{{ $tx->uuid }}')"
                                    wire:confirm="Confirma o estorno desta transação?"
                                    wire:loading.attr="disabled"
                                    class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Estornar
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
