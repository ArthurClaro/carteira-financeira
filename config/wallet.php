<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate limiting das operações financeiras
    |--------------------------------------------------------------------------
    |
    | Limita depósitos e transferências por usuário numa janela de tempo.
    | Defesa contra abuso/DoS e rajadas de duplo-clique. Mantido em config
    | (sem números mágicos no código) e ajustável por ambiente via env.
    |
    */

    'throttle' => [
        'max_attempts' => (int) env('WALLET_THROTTLE_MAX_ATTEMPTS', 10),
        'decay_seconds' => (int) env('WALLET_THROTTLE_DECAY_SECONDS', 60),
    ],

];
