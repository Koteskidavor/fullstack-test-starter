<?php

declare(strict_types=1);

namespace App\Models\Product;

use App\Models\Price;
use App\Models\Attribute\AbstractAttribute;
use InvalidArgumentException;

class ProductFactory
{
    /**
     * @param Price[] $prices
     * @param AbstractAttribute[] $attributes
     */
    public static function create(
        string $id,
        string $name,
        string $brand,
        string $description,
        string $category,
        array $prices = [],
        array $attributes = []
    ): AbstractProduct {
        return match ($category) {
            'clothes' => new ClothingProduct(
                $id,
                $name,
                $brand,
                $description,
                $category,
                $prices,
                $attributes
            ),
            'tech' => new TechProduct(
                $id,
                $name,
                $brand,
                $description,
                $category,
                $prices,
                $attributes
            ),
            default => throw new InvalidArgumentException("Unsupported product category: {$category}")
        };
    }
}