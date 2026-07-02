<?php

use App\Enums\TransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
| Teste de concorrência REAL contra MySQL. A suíte padrão roda em SQLite
| :memory:, onde lockForUpdate é no-op e o caminho crítico do WalletService
| nunca é exercitado. Aqui, N processos concorrentes (pcntl_fork) disputam um
| saldo que cobre apenas K transferências: sob lock pessimista correto,
| exatamente K sucedem e o saldo nunca fica negativo. Sem lock, haveria lost
| update -> mais de K sucessos e saldo negativo, falhando o teste.
|
| Conexão dedicada "concurrency" (env CONCURRENCY_DB_*), isolada do run padrão:
| sem essas envs (ou sem a extensão pcntl), o teste se auto-pula.
*/

beforeEach(function () {
    if (! function_exists('pcntl_fork') || ! env('CONCURRENCY_DB_HOST')) {
        $this->markTestSkipped('Requer a extensão pcntl e um MySQL acessível (CONCURRENCY_DB_HOST).');
    }

    config([
        'database.connections.concurrency' => [
            'driver' => 'mysql',
            'host' => env('CONCURRENCY_DB_HOST'),
            'port' => env('CONCURRENCY_DB_PORT', 3306),
            'database' => env('CONCURRENCY_DB_DATABASE', 'wallet'),
            'username' => env('CONCURRENCY_DB_USERNAME', 'root'),
            'password' => env('CONCURRENCY_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ],
        'database.default' => 'concurrency',
    ]);

    Artisan::call('migrate:fresh', ['--database' => 'concurrency', '--force' => true]);
});

it('impede double-spend em transferências concorrentes', function () {
    $amountCents = 100;   // R$ 1,00 por transferência
    $successful = 5;      // saldo cobre exatamente 5 transferências
    $concurrency = 10;    // 10 processos disputam simultaneamente

    $sender = User::factory()->withBalanceCents($amountCents * $successful)->create();
    $receiver = User::factory()->withBalanceCents(0)->create();
    $senderWalletId = $sender->wallet->id;
    $receiverWalletId = $receiver->wallet->id;

    $pids = [];
    for ($i = 0; $i < $concurrency; $i++) {
        $pid = pcntl_fork();

        if ($pid === -1) {
            $this->fail('Não foi possível criar o processo filho (pcntl_fork).');
        }

        if ($pid === 0) {
            // Processo filho: conexão própria + uma única tentativa de transferência.
            DB::purge('concurrency');
            usleep(20000); // barreira curta p/ os filhos colidirem no lock ao mesmo tempo

            try {
                app(WalletService::class)->transfer(
                    Wallet::on('concurrency')->find($senderWalletId),
                    Wallet::on('concurrency')->find($receiverWalletId),
                    $amountCents,
                );
                exit(0); // sucesso
            } catch (InsufficientBalanceException) {
                exit(1); // rejeitada por saldo — esperado para as excedentes
            } catch (Throwable) {
                exit(2); // erro inesperado
            }
        }

        $pids[] = $pid;
    }

    $succeeded = $rejected = $unexpected = 0;
    foreach ($pids as $pid) {
        pcntl_waitpid($pid, $status);

        match (pcntl_wexitstatus($status)) {
            0 => $succeeded++,
            1 => $rejected++,
            default => $unexpected++,
        };
    }

    DB::purge('concurrency'); // conexão limpa no pai para ler o estado final

    expect($unexpected)->toBe(0)
        ->and($succeeded)->toBe($successful)
        ->and($rejected)->toBe($concurrency - $successful)
        ->and(Wallet::on('concurrency')->find($senderWalletId)->balance_cents)->toBe(0)
        ->and(Wallet::on('concurrency')->find($receiverWalletId)->balance_cents)->toBe($amountCents * $successful)
        ->and(Transaction::on('concurrency')->where('type', TransactionType::Transfer)->count())->toBe($successful);
})->group('concurrency');
