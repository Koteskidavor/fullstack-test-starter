<?php
declare(strict_types=1);

namespace App\Models\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function resolveSpecifics(): void
    {
        $this->items = array_values(
            array_filter(
                array_map(function (array $item): array {
                    $item['value'] = $this->normalizeText($item['value']);
                    return $item;
                }, $this->items),
                fn(array $item): bool => $this->isValidText($item['value'])
            )
        );
    }

    private function normalizeText(string $value): string
    {
        $value = trim($value);

        $value = preg_replace('/\s+/', ' ', $value);

        return strtoupper($value);
    }

    private function isValidText(string $value): bool
    {
        return $value !== '';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'isTextAttribute' => true,
        ]);
    }
}
