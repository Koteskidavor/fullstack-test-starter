<?php
declare(strict_types=1);

namespace App\Repositories;

use Exception;
use PDO;
use App\Models\Order;

class OrderRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function create(array $items): Order
    {
        try {
            $this->pdo->beginTransaction();

            $currency = $items[0]['currency_label'] ?? 'USD';
            $productIds = array_unique(array_column($items, 'product_id'));
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $dataStmt = $this->pdo->prepare("
                SELECT p.id, p.name as product_name, pr.amount
                FROM products p
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.currency = ?
                WHERE p.id IN ($placeholders)
            ");

            $dataStmt->execute(array_merge([$currency], $productIds));
            $dbProducts = [];

            while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
                $dbProducts[$row['id']] = $row;
            }

            foreach ($items as $item) {
                if (!isset($dbProducts[$item['product_id']])) {
                    throw new Exception("Product '{$item['product_id']}' not found");
                }
            }

            $totalAmount = 0.00;

            foreach ($items as $item) {
                $dbAmount = $dbProducts[$item['product_id']]['amount'] ?? null;
                $price = $dbAmount !== null ? (float) $dbAmount : (float) $item['price_amount'];
                $totalAmount += $price * (int) $item['quantity'];
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (total_amount, total_currency, created_at)
                 VALUES (?, ?, NOW())"
            );

            $stmt->execute([$totalAmount, $currency]);
            $orderId = (int) $this->pdo->lastInsertId();
            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items
                (order_id, product_id, product_name, attribute_values, quantity, paid_amount, paid_currency)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($items as $item) {
                $id = $item['product_id'];
                $dbAmount = $dbProducts[$id]['amount'] ?? null;
                $paidAmount = $dbAmount !== null ? (float) $dbAmount : (float) $item['price_amount'];
                $productName = $dbProducts[$id]['product_name'] ?? $id;
                $itemStmt->execute([
                    $orderId,
                    $id,
                    $productName,
                    json_encode($item['attributes'] ?? []),
                    (int) $item['quantity'],
                    $paidAmount,
                    $currency
                ]);
            }
            $this->pdo->commit();

            return new Order([
                'id' => $orderId,
                'totalAmount' => $totalAmount,
                'totalCurrency' => $currency,
                'items' => $items,
                'message' => 'Order placed successfully',
            ]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
