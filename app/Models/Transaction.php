<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Support\Money;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property TransactionType $type
 * @property TransactionStatus $status
 * @property int|null $from_wallet_id
 * @property int|null $to_wallet_id
 * @property int $amount_cents
 * @property int|null $related_transaction_id
 * @property string|null $idempotency_key
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'uuid',
    'type',
    'status',
    'from_wallet_id',
    'to_wallet_id',
    'amount_cents',
    'related_transaction_id',
    'idempotency_key',
    'metadata',
])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount_cents' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction): void {
            if (empty($transaction->uuid)) {
                $transaction->uuid = (string) Str::uuid();
            }
        });
    }

    /** Expõe transações por uuid nas rotas (não pelo id sequencial). */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /** @return BelongsTo<Wallet, $this> */
    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    /** @return BelongsTo<Wallet, $this> */
    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }

    /**
     * Transação original (preenchida quando este registro é um estorno).
     *
     * @return BelongsTo<Transaction, $this>
     */
    public function original(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    public function amount(): Money
    {
        return Money::fromCents($this->amount_cents);
    }

    public function isReversed(): bool
    {
        return $this->status === TransactionStatus::Reversed;
    }

    public function isReversal(): bool
    {
        return $this->type === TransactionType::Reversal;
    }
}
