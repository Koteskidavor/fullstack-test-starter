<?php
declare(strict_types=1);

namespace App\Models;

class Attribute
{
    private string $id;
    private string $name;
    private string $type;
    private array $items = [];

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->type = strtolower($data['type'] ?? 'type');
        $this->items = $data['items'] ?? [];
        $this->resolveSpecifics();
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getItems(): array
    {
        return $this->items;
    }

    public function resolveSpecifics(): void
    {
        if ($this->type === 'swatch') {
            $this->resolveSwatchSpecifics();
        } else {
            $this->resolveTextSpecifics();
        }
    }
    private function resolveSwatchSpecifics(): void
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
    private function resolveTextSpecifics(): void
    {
        $this->items = array_values(
            array_filter(
                array_map(function (array $item): array {
                    $item['value'] = $this->normalizeText($item['value']);
                    return $item;
                }, $this->items),
                fn(array $item): bool => $item['value'] !== ''
            )
        );
    }
    private function normalizeText(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);
        return strtoupper($value);
    }
    private function isValidHexColor(string $value): bool
    {
        return (bool) preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $value);
    }
}