<?php
declare(strict_types=1);

namespace App\Models\Attribute;

final class SwatchAttribute extends AbstractAttribute
{
    public function __construct(string $id, string $name, array $items = [])
    {
        parent::__construct($id, $name, 'swatch', $items);
    }
}