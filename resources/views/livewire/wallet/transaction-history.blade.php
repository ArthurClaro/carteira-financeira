<div class="rounded-xl border border-brand-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-lg font-semibold">Extrato</h2>

    @if ($flash)
        <div class="animate-slide-in mb-4 rounded-lg border px-3 py-2 text-sm {{ $flashIsError ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800' }}">
            {{ $flash }}
        </div>
    @endif

    @if ($transactions->isEmpty())
        <div class="py-10 text-center">
            <svg class="mx-auto size-10 text-brand-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v8.5A2.25 2.25 0 0 1 18.75 19H5.25A2.25 2.25 0 0 1 3 16.75v-8.5ZM3 10h18"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-gray-700">Nenhuma transação ainda</p>
            <p class="mt-1 text-sm text-gray-500">Faça um depósito para começar a movimentar sua carteira.</p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach ($transactions as $tx)
                @php
                    $incoming = $tx->to_wallet_id === $walletId;
                    $canReverse = $tx->status === \App\Enums\TransactionStatus::Completed
                        && ! $tx->isReversal()
                        && auth()->user()->can('reverse', $tx);
                @endphp
                <div class="-mx-3 flex items-center justify-between gap-4 rounded-lg px-3 py-3 transition hover:bg-brand-50/60" wire:key="tx-{{ $tx->id }}">
                    <div class="flex items-center gap-3">
                        {{-- Ícone por tipo/direção --}}
                        @if ($tx->isReversal())
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 4 10l5-5M4 10h11a5 5 0 0 1 0 10h-3"/>
                                </svg>
                            </span>
                        @elseif ($incoming)
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 7 7 17M7 9v8h8"/>
                                </svg>
                            </span>
                        @else
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M17 15V7H9"/>
                                </svg>
                            </span>
                        @endif

                        <div>
                            <p class="font-medium text-gray-900">{{ $tx->type->label() }}</p>
                            <p class="text-xs text-gray-500">{{ $tx->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($tx->status === \App\Enums\TransactionStatus::Reversed)
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">Estornada</span>
                        @endif

                        <span class="font-semibold tabular-nums {{ $incoming ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $incoming ? '+' : '−' }} {{ $tx->amount()->format() }}
                        </span>

                        @if ($canReverse)
                            <button type="button"
                                    wire:click="reverse('{{ $tx->uuid }}')"
                                    wire:confirm="Confirma o estorno desta transação?"
                                    wire:loading.attr="disabled"
                                    class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-800">
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
