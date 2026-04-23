<?php
declare(strict_types=1);

namespace App\Models;

class Price
{
    private float $amount;
    private array $currencyData;

    public function __construct(float $amount, array $currencyData)
    {
        $this->amount = $amount;
        $this->currencyData = $currencyData;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): array
    {
        return $this->currencyData;
    }
}