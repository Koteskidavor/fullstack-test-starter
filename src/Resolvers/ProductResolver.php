<?php
declare(strict_types=1);
namespace App\Resolvers;

use App\Database;
use App\Factories\ProductFactory;
use PDO;

final class ProductResolver
{
    public static function resolveAll(?string $category = null): array
    {
        $pdo = Database::getConnection();

        if ($category && $category !== 'all') {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
            $stmt->execute([$category]);
        }
        else {
            $stmt = $pdo->query("SELECT * FROM products");
        }

        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($productsData as $productData) {
            $productData = self::fetchFullProductData($productData, $pdo);
            $product = ProductFactory::create($productData);
            $products[] = $product->toArray();
        }

        return $products;
    }

    public static function resolveByID(string $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$productData) {
            return null;
        }

        $productData = self::fetchFullProductData($productData, $pdo);
        $product = ProductFactory::create($productData);

        return $product->toArray();
    }

    private static function fetchFullProductData(array $product, PDO $pdo): array
    {
        $priceStmt = $pdo->prepare("
            SELECT p.amount, p.currency as label, COALESCE(c.symbol, '$') as symbol
            FROM prices p
            LEFT JOIN currencies c ON p.currency = c.label
            WHERE p.product_id = ?
        ");
        $priceStmt->execute([$product['id']]);

        $prices = $priceStmt->fetchAll(PDO::FETCH_ASSOC);
        $product['db_prices'] = array_map(fn($row) => [
        'amount' => (float)$row['amount'],
        'currency' => [
        'label' => $row['label'],
        'symbol' => $row['symbol']
        ]
        ], $prices);

        return $product;
    }
}