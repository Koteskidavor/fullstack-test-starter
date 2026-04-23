<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Repositories\OrderRepository;
use App\Models\Order;
use Exception;
use InvalidArgumentException;
use RuntimeException;

final class OrderResolver
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $items): Order
    {
        foreach ($items as $index => $item) {
            if (!isset($item['product_id'])) {
                throw new InvalidArgumentException("Item at index {$index} missing product_id");
            }

            if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                throw new InvalidArgumentException("Item at index {$index} must have positive quantity");
            }
        }

        try {
            return $this->orderRepository->create($items);
        } catch (Exception $e) {
            throw new RuntimeException('Failed to create order: ' . $e->getMessage());
        }
    }
}
