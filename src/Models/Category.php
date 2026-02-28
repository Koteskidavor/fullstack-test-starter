<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;

final class Category
{
    public function __construct(
        public string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function findAll(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->query("SELECT name FROM categories");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn($row) => new self($row['name']),
            $rows
        );
    }
}