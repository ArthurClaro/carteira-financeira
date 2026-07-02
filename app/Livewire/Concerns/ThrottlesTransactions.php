<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait ThrottlesTransactions
{
    /**
     * Garante que o usuário autenticado não exceda o limite de operações
     * financeiras por janela de tempo (defesa contra abuso/DoS e rajadas de
     * duplo-clique). Lança ValidationException — capturada pelo Livewire e
     * exibida no campo informado — quando o limite é atingido.
     */
    protected function ensureTransactionsAreNotRateLimited(string $operation, string $field): void
    {
        $key = 'wallet-op:'.$operation.':'.Auth::id();

        if (RateLimiter::tooManyAttempts($key, (int) config('wallet.throttle.max_attempts'))) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                $field => "Muitas operações em sequência. Tente novamente em {$seconds} segundos.",
            ]);
        }

        RateLimiter::hit($key, (int) config('wallet.throttle.decay_seconds'));
    }
}
