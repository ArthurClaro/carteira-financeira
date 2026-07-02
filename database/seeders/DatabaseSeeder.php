<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Usuários de demonstração. O UserObserver cria a carteira automaticamente
     * (por isso NÃO usamos WithoutModelEvents aqui). Os saldos iniciais entram
     * como depósitos reais via WalletService — assim o ledger permanece a fonte
     * da verdade e `php artisan wallet:reconcile` fecha sem divergência.
     */
    public function run(): void
    {
        $wallet = app(WalletService::class);

        $alice = User::factory()->create([
            'name' => 'Alice Silva',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
        ]);
        $wallet->deposit($alice->wallet, 100_000); // R$ 1.000,00

        $bob = User::factory()->create([
            'name' => 'Bob Souza',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
        ]);
        $wallet->deposit($bob->wallet, 5_000); // R$ 50,00

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
