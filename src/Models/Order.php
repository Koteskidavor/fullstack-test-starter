<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;
use Exception;

final class Order
{
    public static function create(array $items): int
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $totalAmount = array_reduce(
                $items,
                fn($sum, $item) => $sum + ((float) $item['price_amount'] * (int) $item['quantity']),
                0
            );

            $stmt = $pdo->prepare(
                "INSERT INTO orders (total_amount, total_currency, created_at) 
                 VALUES (?, ?, NOW())"
            );
            $stmt->execute([$totalAmount, $items[0]['currency_label'] ?? 'USD']);
            $orderId = (int) $pdo->lastInsertId();


            $itemStmt = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, product_id, product_name, attribute_values, quantity, paid_amount, paid_currency)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $dataStmt = $pdo->prepare("
                SELECT p.name as product_name, pr.amount 
                FROM products p 
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.currency = ?
                WHERE p.id = ?
            ");

            foreach ($items as $item) {
                $dataStmt->execute([$item['currency_label'], $item['product_id']]);
                $productData = $dataStmt->fetch();

                $productName = $productData['product_name'] ?? $item['product_id'];
                $paidAmount = $productData['amount'] ?? (float) $item['price_amount'];

                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $productName,
                    json_encode($item['attributes'] ?? []),
                    (int) $item['quantity'],
                    (float) $paidAmount,
                    $item['currency_label']
                ]);
            }

            $pdo->commit();

            return $orderId;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}