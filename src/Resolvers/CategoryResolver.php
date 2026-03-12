<?php

namespace App\Resolvers;

use App\Models\Category;

class CategoryResolver
{
    public static function resolveAll(): array
    {
        return Category::findAll();
    }
}