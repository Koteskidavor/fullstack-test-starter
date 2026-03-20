<?php
declare(strict_types=1);

namespace App\Resolvers;

use App\Database;
use App\Factories\AttributeFactory;
use PDO;

final class AttributeResolver
{
    /**
     * Resolves and returns a list of attributes for a specific product.
     * 
     * @param string $productId
     * @return array
     */
    public static function resolveByProductId(string $productId): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT a.id, a.name, a.type, pa.id as pa_id, pa.displayValue, pa.value
            FROM product_attributes pa
            JOIN attributes a ON pa.attribute_id = a.id
            WHERE pa.product_id = ?
            ORDER BY pa.sort_order ASC
        ");
        $stmt->execute([$productId]);

        $attrsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];

        foreach ($attrsRaw as $row) {
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

        $attributes = [];
        foreach ($grouped as $data) {
            $attribute = AttributeFactory::create($data);
            // Polmorphic specifics can be handled here if needed, 
            // but the Model's constructor or resolveSpecifics already handles it.
            if (method_exists($attribute, 'resolveSpecifics')) {
                $attribute->resolveSpecifics();
            }
            $attributes[] = $attribute->toArray();
        }

        return $attributes;
    }
}