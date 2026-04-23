<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Repositories\PriceRepository;
use Exception;
use RuntimeException;

final class PriceResolver
{
    private PriceRepository $priceRepository;

    public function __construct(PriceRepository $priceRepository)
    {
        $this->priceRepository = $priceRepository;
    }
    public function resolvePrices(string $productId): array
    {
        try {
            return $this->priceRepository->findByProductId($productId);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to retrieve prices for product {$productId}: " . $e->getMessage());
        }
    }
}