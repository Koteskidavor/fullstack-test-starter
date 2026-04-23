<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Attribute;

class AttributeRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function findByProductId(string $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.id, a.name, a.type, pa.id as pa_id, pa.displayValue, pa.value
            FROM product_attributes pa
            JOIN attributes a ON pa.attribute_id = a.id
            WHERE pa.product_id = ?
            ORDER BY pa.sort_order ASC
        ");
        $stmt->execute([$productId]);

        $grouped = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($grouped[$row['id']])) {
                $grouped[$row['id']] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => []
                ];
            }
            $grouped[$row['id']]['items'][] = [
                'id' => $row['pa_id'],
                'displayValue' => $row['displayValue'],
                'value' => $row['value']
            ];
        }

        return array_map(fn($data) => new Attribute($data), $grouped);
    }
}
