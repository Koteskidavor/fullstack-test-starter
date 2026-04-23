<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Repositories\ProductRepository;
use App\Resolvers\PriceResolver;
use App\Models\Product;
use Exception;
use RuntimeException;

final class ProductResolver
{
    private ProductRepository $productRepository;
    private PriceResolver $priceResolver;

    public function __construct(ProductRepository $productRepository, PriceResolver $priceResolver)
    {
        $this->productRepository = $productRepository;
        $this->priceResolver = $priceResolver;
    }

    public function resolveAll(?string $category = null): array
    {
        try {
            $products = $this->productRepository->findAll($category);

            if (empty($products)) {
                throw new RuntimeException('No products found' . ($category ? " in category '{$category}'" : ''));
            }

            return $products;
        } catch (Exception $e) {
            throw new RuntimeException('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    public function resolveById(string $id): ?Product
    {
        try {
            $product = $this->productRepository->findById($id);

            return $product;
        } catch (Exception $e) {
            throw new RuntimeException("Failed to retrieve product {$id}: " . $e->getMessage());
        }
    }
    public function resolvePrices(string $productId): array
    {
        try {
            return $this->priceResolver->resolvePrices($productId);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to retrieve prices for product {$productId}: " . $e->getMessage());
        }
    }
}
