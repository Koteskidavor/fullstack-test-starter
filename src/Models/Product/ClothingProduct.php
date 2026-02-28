<?php

declare(strict_types=1);

namespace App\Models\Product;

class ClothingProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'clothes';
    }
}