<?php
declare(strict_types=1);
namespace App\Resolvers;

use App\Models\Category;

class CategoryResolver
{
    public static function resolveAll(): array
    {
        $categories = Category::findAll();

        $result = [];
        foreach ($categories as $catModel) {
            $result[] = [
                'name' => $catModel->getName()
            ];
        }
        return $result;
    }
}