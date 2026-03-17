<?php
declare(strict_types=1);

namespace App\Models\Product;

use App\Models\Attribute\AbstractAttribute;
use App\Factories\AttributeFactory;

abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected bool $inStock;
    protected array $gallery;
    protected string $description;
    protected string $category;
    protected string $brand;
    protected array $prices = [];
    protected array $attributes = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->inStock = (bool) $data['inStock'];
        $this->gallery = json_decode($data['gallery'] ?? '[]', true) ?? [];
        $this->description = $data['description'] ?? '';
        $this->category = $data['category'];
        $this->brand = $data['brand'] ?? '';

        if (isset($data['db_prices'])) {
            $this->prices = $data['db_prices'];
        }

        if (isset($data['db_attributes'])) {
            $this->setAttributes($data['db_attributes']);
        }
    }

    private function setAttributes(array $rawAttributes): void
    {
        foreach ($rawAttributes as $attrData) {
            $this->attributes[] = AttributeFactory::create($attrData);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getInStock(): bool
    {
        return $this->inStock;
    }
    public function getGallery(): array
    {
        return $this->gallery;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getCategory(): string
    {
        return $this->category;
    }
    public function getBrand(): string
    {
        return $this->brand;
    }
    public function getPrices(): array
    {
        return $this->prices;
    }
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    abstract public function getSpecialType(): string;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'inStock' => $this->inStock,
            'gallery' => $this->gallery,
            'description' => $this->description,
            'category' => $this->category,
            'brand' => $this->brand,
            'prices' => $this->prices,
            'attributes' => array_map(fn($attr) => $attr->toArray(), $this->attributes)
        ];
    }
}
