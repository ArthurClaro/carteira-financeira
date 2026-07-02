<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Usuários de demonstração. O UserObserver cria a carteira automaticamente
     * (por isso NÃO usamos WithoutModelEvents aqui); ajustamos o saldo em seguida.
     */
    public function run(): void
    {
        $alice = User::factory()->create([
            'name' => 'Alice Silva',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
        ]);
        $alice->wallet()->update(['balance_cents' => 100_000]); // R$ 1.000,00

        $bob = User::factory()->create([
            'name' => 'Bob Souza',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
        ]);
        $bob->wallet()->update(['balance_cents' => 5_000]); // R$ 50,00

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
