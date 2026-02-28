<?php

declare(strict_types=1);

namespace App\Models\Product;

use App\Models\Price;
use App\Models\Attribute\AbstractAttribute;

abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected string $brand;
    protected string $description;
    protected string $category;

    protected array $prices = [];

    protected array $attributes = [];

    public function __construct(
        string $id,
        string $name,
        string $brand,
        string $description,
        string $category,
        array $prices = [],
        array $attributes = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->brand = $brand;
        $this->description = $description;
        $this->category = $category;

        /** @var Price[] */
        $this->prices = $prices;

        /** @var AbstractAttribute[] */
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }


    /**
     * @return Price[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @return AbstractAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    abstract public function getType(): string;
}