<?php
declare(strict_types=1);
namespace App\Resolvers;

use App\Database;
use App\Factories\ProductFactory;
use PDO;

final class ProductResolver
{
    public static function resolveAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM products");
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
            'amount' => (float) $row['amount'],
            'currency' => [
                'label' => $row['label'],
                'symbol' => $row['symbol']
            ]
        ], $prices);

        $attrStmt = $pdo->prepare("
            SELECT a.id, a.name, a.type, pa.id as pa_id, pa.displayValue, pa.value
            FROM product_attributes pa
            JOIN attributes a ON pa.attribute_id = a.id
            WHERE pa.product_id = ?
            ORDER BY pa.sort_order ASC
        ");
        $attrStmt->execute([$product['id']]);

        $attrs = [];
        foreach ($attrStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($attrs[$row['id']])) {
                $attrs[$row['id']] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => []
                ];
            }
            $attrs[$row['id']]['items'][] = [
                'id' => $row['pa_id'],
                'displayValue' => $row['displayValue'],
                'value' => $row['value']
            ];
        }

        $product['db_attributes'] = array_values($attrs);

        return $product;
    }
}