<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'string' => 'O campo :attribute deve ser um texto.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'unique' => 'Este :attribute já está em uso.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
    ],
    'decimal' => 'O campo :attribute deve ter no máximo :decimal casas decimais.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
    ],
    'max' => [
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'amount' => 'valor',
        'recipient_email' => 'e-mail do destinatário',
    ],

    'custom' => [],
];
