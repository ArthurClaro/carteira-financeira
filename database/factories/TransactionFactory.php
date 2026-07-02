<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::Completed,
            'from_wallet_id' => null,
            'to_wallet_id' => fn () => User::factory()->create()->wallet?->id,
            'amount_cents' => fake()->numberBetween(100, 100_000),
            'idempotency_key' => null,
            'metadata' => null,
        ];
    }

    public function transfer(): static
    {
        return $this->state(fn () => [
            'type' => TransactionType::Transfer,
            'from_wallet_id' => fn () => User::factory()->create()->wallet?->id,
        ]);
    }

    public function reversed(): static
    {
        return $this->state(fn () => [
            'status' => TransactionStatus::Reversed,
        ]);
    }
}
