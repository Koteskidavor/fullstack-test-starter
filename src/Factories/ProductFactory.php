<?php
declare(strict_types=1);

namespace App\Factories;

use App\Models\Product\AbstractProduct;
use App\Models\Product\TechProduct;
use App\Models\Product\ClothesProduct;

class ProductFactory
{
    private const PRODUCT_MAP = [
        'clothes' => ClothesProduct::class,
    ];
    public static function create(array $data): AbstractProduct
    {
        $category = strtolower($data['category'] ?? '');

        $class = self::PRODUCT_MAP[$category] ?? TechProduct::class;

        return new $class($data);
    }
}
