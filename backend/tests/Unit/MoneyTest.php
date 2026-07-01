<?php

use App\Support\Money;

it('cria a partir de centavos', function () {
    expect(Money::fromCents(12345)->cents())->toBe(12345);
});

it('converte reais para centavos sem erro de float', function (string|int $input, int $expected) {
    expect(Money::fromReais($input)->cents())->toBe($expected);
})->with([
    ['100', 10000],
    ['100.5', 10050],
    ['100.55', 10055],
    ['0.99', 99],
    ['0,99', 99],            // aceita vírgula como separador decimal
    ['1234.56', 123456],
    [10, 1000],
]);

it('rejeita valores monetários inválidos', function (string $input) {
    Money::fromReais($input);
})->with(['abc', '1.234', '10.999', ''])->throws(InvalidArgumentException::class);

it('formata em BRL', function (int $cents, string $expected) {
    expect(Money::fromCents($cents)->format())->toBe($expected);
})->with([
    [0, 'R$ 0,00'],
    [99, 'R$ 0,99'],
    [10050, 'R$ 100,50'],
    [123456, 'R$ 1.234,56'],
    [-1990, '-R$ 19,90'],
    [100000000, 'R$ 1.000.000,00'],
]);
