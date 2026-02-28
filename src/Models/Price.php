<?php
declare(strict_types=1);

namespace App\Models;

final class Price
{
    public function __construct(
        private float $amount,
        private string $currencyLabel,
        private string $currencySymbol
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrencyLabel(): string
    {
        return $this->currencyLabel;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }
}