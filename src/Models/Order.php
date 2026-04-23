<?php
declare(strict_types=1);

namespace App\Models;

class Order
{
    private int $id;
    private float $totalAmount;
    private string $totalCurrency;
    private array $items = [];
    private string $message;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->totalAmount = $data['totalAmount'] ?? 0.0;
        $this->totalCurrency = $data['totalCurrency'] ?? 'USD';
        $this->message = $data['message'] ?? '';
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }
    public function getTotalCurrency(): string
    {
        return $this->totalCurrency;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
}