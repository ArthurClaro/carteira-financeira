<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Detecção ativa de inconsistência: como o ledger (transactions) é a fonte da
 * verdade, o saldo de cada carteira deve ser igual a créditos − débitos. Qualquer
 * divergência indica corrupção de dados (escrita fora do WalletService, falha de
 * infra) e a transação divergente é candidata a estorno.
 */
class ReconcileWallets extends Command
{
    protected $signature = 'wallet:reconcile';

    protected $description = 'Confere se o saldo de cada carteira bate com a soma do ledger (transactions)';

    public function handle(): int
    {
        $credits = DB::table('transactions')
            ->whereNotNull('to_wallet_id')
            ->groupBy('to_wallet_id')
            ->select('to_wallet_id', DB::raw('SUM(amount_cents) as total'))
            ->pluck('total', 'to_wallet_id');

        $debits = DB::table('transactions')
            ->whereNotNull('from_wallet_id')
            ->groupBy('from_wallet_id')
            ->select('from_wallet_id', DB::raw('SUM(amount_cents) as total'))
            ->pluck('total', 'from_wallet_id');

        $divergences = [];
        $total = 0;

        Wallet::query()->orderBy('id')->chunkById(500, function ($wallets) use ($credits, $debits, &$divergences, &$total) {
            foreach ($wallets as $wallet) {
                $total++;
                $expected = (int) ($credits[$wallet->id] ?? 0) - (int) ($debits[$wallet->id] ?? 0);

                if ($expected !== $wallet->balance_cents) {
                    $divergences[] = [
                        'wallet_id' => $wallet->id,
                        'user_id' => $wallet->user_id,
                        'saldo_ledger' => $expected,
                        'saldo_atual' => $wallet->balance_cents,
                        'diferenca' => $wallet->balance_cents - $expected,
                    ];
                }
            }
        });

        if ($divergences === []) {
            $this->info("Todas as {$total} carteiras estão consistentes com o ledger.");

            return self::SUCCESS;
        }

        $this->error(count($divergences).' carteira(s) divergente(s) do ledger:');
        $this->table(
            ['Wallet', 'User', 'Saldo pelo ledger', 'Saldo atual', 'Diferença (centavos)'],
            $divergences,
        );

        return self::FAILURE;
    }
}
