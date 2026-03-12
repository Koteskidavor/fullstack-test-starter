<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Models\Order;

final class OrderResolver
{
    public static function createOrder(array $items): array
    {
        $orderId = Order::create($items);

        return [
            'id' => (string) $orderId,
            'message' => 'Order created successfully',
        ];
    }
}