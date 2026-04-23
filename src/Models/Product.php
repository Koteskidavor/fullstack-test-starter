<?php
declare(strict_types=1);

namespace App\Models;

class Product
{
    private string $id;
    private string $name;
    private bool $inStock;
    private array $gallery;
    private string $description;
    private string $category;
    private string $brand;
    private array $prices = [];
    private array $attributes = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->inStock = (bool) ($data['inStock'] ?? false);
        $this->gallery = $data['gallery'] ?? [];
        $this->description = $data['description'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->brand = $data['brand'] ?? '';
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
}