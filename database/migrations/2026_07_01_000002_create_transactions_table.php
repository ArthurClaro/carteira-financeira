<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // deposit | transfer | reversal
            $table->string('type');
            // completed | reversed
            $table->string('status');

            // Origem (débito) e destino (crédito). Depósito: só destino.
            $table->foreignId('from_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->foreignId('to_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();

            // Magnitude sempre positiva; o sentido é dado por from/to.
            $table->unsignedBigInteger('amount_cents');

            // Em estornos, aponta para a transação original.
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();

            // Idempotência: impede processar a mesma operação duas vezes.
            $table->string('idempotency_key')->nullable()->unique();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('from_wallet_id');
            $table->index('to_wallet_id');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
