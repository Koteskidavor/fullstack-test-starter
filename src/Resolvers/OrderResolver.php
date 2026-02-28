<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Models\Order;

final class OrderResolver
{
    public static function createOrder(array $input): array
    {
        $orderId = Order::create($input['items']);

        return [
            'id' => $orderId,
            'message' => 'Order created successfully',
        ];
    }
}