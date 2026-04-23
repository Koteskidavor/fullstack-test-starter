<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Repositories\AttributeRepository;
use Exception;
use RuntimeException;

final class AttributeResolver
{
    private AttributeRepository $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function resolveByProductId(string $productId): array
    {
        try {
            return $this->attributeRepository->findByProductId($productId);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to retrieve attributes for product {$productId}: " . $e->getMessage());
        }
    }
}
