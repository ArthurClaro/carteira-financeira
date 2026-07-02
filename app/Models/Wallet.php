<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $balance_cents
 * @property string $currency
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['user_id', 'balance_cents', 'currency'])]
class Wallet extends Model
{
    protected function casts(): array
    {
        return [
            'balance_cents' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Transaction, $this> */
    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_wallet_id');
    }

    /** @return HasMany<Transaction, $this> */
    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_wallet_id');
    }

    public function balance(): Money
    {
        return Money::fromCents($this->balance_cents);
    }
}
