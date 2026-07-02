<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Value Object monetário. Armazena o valor em centavos (inteiro) para evitar
 * qualquer erro de ponto flutuante — regra crítica em aplicações financeiras.
 */
final class Money
{
    private function __construct(public readonly int $cents) {}

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    /**
     * Cria a partir de um valor em reais (string/número) com até 2 casas decimais.
     * Usa bcmath para converter sem passar por float — sem arredondamento silencioso.
     */
    public static function fromReais(string|int|float $amount): self
    {
        $normalized = str_replace(',', '.', (string) $amount);

        if (! preg_match('/^-?\d+(\.\d{1,2})?$/', $normalized)) {
            throw new InvalidArgumentException("Valor monetário inválido: {$amount}");
        }

        return new self((int) bcmul($normalized, '100', 0));
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function isPositive(): bool
    {
        return $this->cents > 0;
    }

    public function isNegative(): bool
    {
        return $this->cents < 0;
    }

    /** Formata em BRL: 123456 -> "R$ 1.234,56" (suporta negativos). */
    public function format(): string
    {
        $sign = $this->cents < 0 ? '-' : '';
        $abs = abs($this->cents);
        $reais = number_format(intdiv($abs, 100), 0, ',', '.');

        return sprintf('%sR$ %s,%02d', $sign, $reais, $abs % 100);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
