<?php

namespace App\Resolvers;

use App\Database;
use PDO;


final class ProductResolver
{
    public static function resolveAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product = self::enrichProduct($product, $pdo);
        }

        return $products;

    }
    public static function resolveByID(string $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ? self::enrichProduct($product, $pdo) : null;
    }

    private static function enrichProduct(array $product, PDO $pdo): array
    {
        $product['gallery'] = json_decode($product['gallery'], true) ?? [];
        $product['inStock'] = (bool) $product['inStock'];

        $priceStmt = $pdo->prepare("
            SELECT p.amount, p.currency as label, COALESCE(c.symbol, '$') as symbol
            FROM prices p
            LEFT JOIN currencies c ON p.currency = c.label
            WHERE p.product_id = ?
        ");
        $priceStmt->execute([$product['id']]);

        $prices = $priceStmt->fetchAll(PDO::FETCH_ASSOC);
        $product['prices'] = array_map(fn($row) => [
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

        $product['attributes'] = array_values($attrs);

        return $product;
    }
}