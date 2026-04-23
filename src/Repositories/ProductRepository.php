<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Product;

class ProductRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function findAll(?string $category = null): array
    {
        if ($category && $category !== 'all') {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ?");
            $stmt->execute([$category]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM products");
        }

        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($product) {
            $product['gallery'] = json_decode($product['gallery'] ?? '[]', true) ?? [];
            return new Product($product);
        }, $productsData);
    }

    public function findById(string $id): ?Product
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            return null;
        }
        $product['gallery'] = json_decode($product['gallery'] ?? '[]', true) ?? [];
        return new Product($product);
    }
}
