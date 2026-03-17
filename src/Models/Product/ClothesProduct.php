<?php
declare(strict_types=1);

namespace App\Models\Product;

class ClothesProduct extends AbstractProduct
{
    public function getSpecialType(): string
    {
        return 'Clothes';
    }
}
