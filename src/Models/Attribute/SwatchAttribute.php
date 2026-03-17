<?php
declare(strict_types=1);

namespace App\Models\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function resolveSpecifics(): void
    {
        $this->items = array_values(
            array_filter(
                array_map(function (array $item): array {
                    $item['value'] = strtolower(trim($item['value']));
                    return $item;
                }, $this->items),
                fn(array $item): bool => $this->isValidHexColor($item['value'])
            )
        );
    }

    public function isValidHexColor(string $value): bool
    {
        return (bool) preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $value);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'isColorSwatch' => true,
        ]);
    }
}
