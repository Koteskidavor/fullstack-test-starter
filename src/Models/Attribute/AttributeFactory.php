<?php
declare(strict_types=1);

namespace App\Models\Attribute;

final class AttributeFactory
{
    public static function create(
        string $id,
        string $name,
        string $type,
        array $items
    ): AbstractAttribute {

        $attributeItems = array_map(
            fn($item) => new AttributeItem(
                $item['id'],
                $item['value'],
                $item['display_value']
            ),
            $items,
        );
        return match ($type) {
            'swatch' => new SwatchAttribute($id, $name, $attributeItems),
            default => new TextAttribute($id, $name, $attributeItems),
        };
    }
}