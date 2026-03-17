<?php
declare(strict_types=1);

namespace App\Factories;

use App\Models\Attribute\AbstractAttribute;
use App\Models\Attribute\TextAttribute;
use App\Models\Attribute\SwatchAttribute;

class AttributeFactory
{
    private const ATTRIBUTE_MAP = [
        'swatch' => SwatchAttribute::class,
    ];
    public static function create(array $data): AbstractAttribute
    {
        $type = strtolower($data['type'] ?? 'text');

        $class = self::ATTRIBUTE_MAP[$type] ?? TextAttribute::class;

        return new $class($data);
    }
}
