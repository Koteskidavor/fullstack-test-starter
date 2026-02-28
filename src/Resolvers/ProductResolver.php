<?php

namespace App\Resolvers;

use App\Models\Product\AbstractProduct;
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
            $priceStmt = $pdo->prepare("SELECT amount, currency FROM prices WHERE product_id = ?");
            $priceStmt->execute([$product['id']]);
            $product['prices'] = $priceStmt->fetchAll(PDO::FETCH_ASSOC);

            $attrStmt = $pdo->prepare("
            SELECT a.id, a.name, a.type, pa.id as pa_id, pa.displayValue, pa.value
            FROM product_attributes pa
            JOIN attributes a ON pa.attribute_id = a.id
            WHERE pa.product_id = ?
        ");
            $attrStmt->execute([$product['id']]);
            $attrs = [];
            foreach ($attrStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $attrs[$row['id']]['id'] = $row['id'];
                $attrs[$row['id']]['name'] = $row['name'];
                $attrs[$row['id']]['type'] = $row['type'];
                $attrs[$row['id']]['items'][] = [
                    'id' => $row['pa_id'],
                    'displayValue' => $row['displayValue'],
                    'value' => $row['value']
                ];
            }
            $product['attributes'] = array_values($attrs);
        }

        return $products;
    }

    public static function resolveByID(string $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }
}