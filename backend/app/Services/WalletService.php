<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\CannotReverseReversalException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidAmountException;
use App\Exceptions\SelfTransferException;
use App\Exceptions\TransactionAlreadyReversedException;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Núcleo financeiro. Toda mutação de saldo acontece dentro de uma transação de
 * banco com lock pessimista (lockForUpdate) nas carteiras envolvidas, garantindo
 * atomicidade e ausência de "double-spend" em condições de corrida. Cada movimento
 * gera um registro imutável no ledger (tabela transactions).
 */
class WalletService
{
    /**
     * Depósito: soma ao saldo. Se o saldo estiver negativo, o depósito acrescenta
     * ao valor (ex.: -50 + 30 = -20). Idempotente via chave opcional.
     */
    public function deposit(Wallet $wallet, int $amountCents, ?string $idempotencyKey = null): Transaction
    {
        $this->assertPositiveAmount($amountCents);

        return DB::transaction(function () use ($wallet, $amountCents, $idempotencyKey) {
            if ($existing = $this->existingIdempotent($idempotencyKey)) {
                return $existing;
            }

            $locked = $this->lockWallets($wallet->id)->first();
            $locked->increment('balance_cents', $amountCents);

            return $this->record(
                type: TransactionType::Deposit,
                from: null,
                to: $locked,
                amountCents: $amountCents,
                idempotencyKey: $idempotencyKey,
            );
        }, attempts: 3);
    }

    /**
     * Transferência entre carteiras. Valida saldo suficiente na origem (sob lock)
     * antes de mover os fundos. Débito e crédito são atômicos.
     */
    public function transfer(Wallet $from, Wallet $to, int $amountCents, ?string $idempotencyKey = null): Transaction
    {
        $this->assertPositiveAmount($amountCents);

        if ($from->id === $to->id) {
            throw new SelfTransferException;
        }

        return DB::transaction(function () use ($from, $to, $amountCents, $idempotencyKey) {
            if ($existing = $this->existingIdempotent($idempotencyKey)) {
                return $existing;
            }

            $wallets = $this->lockWallets($from->id, $to->id);
            $lockedFrom = $wallets[$from->id];
            $lockedTo = $wallets[$to->id];

            if ($lockedFrom->balance_cents < $amountCents) {
                throw new InsufficientBalanceException($lockedFrom->balance_cents, $amountCents);
            }

            $lockedFrom->decrement('balance_cents', $amountCents);
            $lockedTo->increment('balance_cents', $amountCents);

            return $this->record(
                type: TransactionType::Transfer,
                from: $lockedFrom,
                to: $lockedTo,
                amountCents: $amountCents,
                idempotencyKey: $idempotencyKey,
            );
        }, attempts: 3);
    }

    /**
     * Estorno: reverte o movimento original, marca-o como `reversed` e cria um
     * lançamento compensatório (ledger append-only — nada é apagado). Só pode ser
     * feito uma vez e não se aplica a estornos. Pode deixar saldo negativo (ex.: o
     * destinatário já gastou o valor) — coerente com a regra de saldo negativo.
     */
    public function reverse(Transaction $transaction): Transaction
    {
        if ($transaction->isReversal()) {
            throw new CannotReverseReversalException;
        }

        if ($transaction->isReversed()) {
            throw new TransactionAlreadyReversedException;
        }

        return DB::transaction(function () use ($transaction) {
            // Revalida o status sob lock da transação original (evita corrida de duplo estorno).
            $original = Transaction::whereKey($transaction->getKey())->lockForUpdate()->firstOrFail();

            if ($original->status === TransactionStatus::Reversed) {
                throw new TransactionAlreadyReversedException;
            }

            $walletIds = array_values(array_filter([$original->from_wallet_id, $original->to_wallet_id]));
            $wallets = $this->lockWallets(...$walletIds);

            $originalTo = $original->to_wallet_id ? $wallets[$original->to_wallet_id] : null;
            $originalFrom = $original->from_wallet_id ? $wallets[$original->from_wallet_id] : null;

            // Movimento inverso: retira de quem recebeu, devolve para quem enviou.
            // Sem checagem de saldo — estorno é permitido mesmo gerando saldo negativo.
            $originalTo?->decrement('balance_cents', $original->amount_cents);
            $originalFrom?->increment('balance_cents', $original->amount_cents);

            $original->update(['status' => TransactionStatus::Reversed]);

            return $this->record(
                type: TransactionType::Reversal,
                from: $originalTo,
                to: $originalFrom,
                amountCents: $original->amount_cents,
                related: $original,
            );
        }, attempts: 3);
    }

    private function assertPositiveAmount(int $amountCents): void
    {
        if ($amountCents <= 0) {
            throw new InvalidAmountException;
        }
    }

    private function existingIdempotent(?string $idempotencyKey): ?Transaction
    {
        if ($idempotencyKey === null) {
            return null;
        }

        return Transaction::where('idempotency_key', $idempotencyKey)->first();
    }

    /**
     * Trava as carteiras informadas em ordem crescente de id (ordem consistente
     * evita deadlock entre transferências concorrentes em sentidos opostos).
     *
     * @return Collection<int, Wallet>
     */
    private function lockWallets(int ...$ids): Collection
    {
        sort($ids);

        return Wallet::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
    }

    private function record(
        TransactionType $type,
        ?Wallet $from,
        ?Wallet $to,
        int $amountCents,
        ?Transaction $related = null,
        ?string $idempotencyKey = null,
    ): Transaction {
        return Transaction::create([
            'type' => $type,
            'status' => TransactionStatus::Completed,
            'from_wallet_id' => $from?->id,
            'to_wallet_id' => $to?->id,
            'amount_cents' => $amountCents,
            'related_transaction_id' => $related?->id,
            'idempotency_key' => $idempotencyKey,
        ]);
    }
}
