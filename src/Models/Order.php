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

            $pdo->exec("INSERT INTO orders (created_at) VALUES (NOW())");
            $orderId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO order_items 
                (order_id, product_id, quantity, price_amount, currency_label, currency_symbol)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price_amount'],
                    $item['currency_label'],
                    $item['currency_symbol']
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