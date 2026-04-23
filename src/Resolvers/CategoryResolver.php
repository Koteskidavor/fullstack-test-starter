<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Repositories\CategoryRepository;
use Exception;
use RuntimeException;

final class CategoryResolver
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function resolveAll(): array
    {
        try {
            return $this->categoryRepository->findAll();
        } catch (Exception $e) {
            throw new RuntimeException('Failed to retrieve categories: ' . $e->getMessage());
        }
    }
}
