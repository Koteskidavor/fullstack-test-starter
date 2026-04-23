<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Category;

class CategoryRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT name FROM categories");

        return array_map(fn($row) => new Category($row['name']), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
