<?php
declare(strict_types=1);

namespace App\Service;

use App\Database;
use PDO;
use Throwable;

final class DataImporter
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function import(string $jsonPath): void
    {
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException('data.json not found.');
        }

        $raw = file_get_contents($jsonPath);
        $decoded = json_decode($raw, true);

        if (!isset($decoded['data'])) {
            throw new \RuntimeException('Invalid JSON structure.');
        }

        $data = $decoded['data'];

        $this->pdo->beginTransaction();

        try {
            $this->insertCategories($data['categories'] ?? []);
            $this->insertProducts($data['products'] ?? []);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function insertCategories(array $categories): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO categories (name) VALUES (:name)'
        );

        foreach ($categories as $cat) {
            $stmt->execute([
                'name' => $cat['name']
            ]);
        }
    }

    private function insertProducts(array $products): void
    {
        foreach ($products as $prod) {

            $this->pdo->prepare(
                'INSERT IGNORE INTO products 
                (id, name, inStock, gallery, description, category, brand)
                VALUES (:id, :name, :inStock, :gallery, :description, :category, :brand)'
            )->execute([
                        'id' => $prod['id'],
                        'name' => $prod['name'],
                        'inStock' => (int) $prod['inStock'],
                        'gallery' => json_encode($prod['gallery']),
                        'description' => $prod['description'],
                        'category' => $prod['category'],
                        'brand' => $prod['brand'],
                    ]);

            $this->insertPrices($prod);
            $this->insertAttributes($prod);
        }
    }

    private function insertPrices(array $prod): void
    {
        foreach ($prod['prices'] ?? [] as $price) {

            $this->pdo->prepare(
                'INSERT IGNORE INTO currencies (label, symbol)
                 VALUES (:label, :symbol)'
            )->execute([
                        'label' => $price['currency']['label'],
                        'symbol' => $price['currency']['symbol']
                    ]);

            $this->pdo->prepare(
                'INSERT IGNORE INTO prices (amount, currency, product_id)
                 VALUES (:amount, :currency, :product_id)'
            )->execute([
                        'amount' => $price['amount'],
                        'currency' => $price['currency']['label'],
                        'product_id' => $prod['id']
                    ]);
        }
    }

    private function insertAttributes(array $prod): void
    {
        foreach ($prod['attributes'] ?? [] as $attr) {

            $this->pdo->prepare(
                'INSERT IGNORE INTO attributes (id, name, type)
                 VALUES (:id, :name, :type)'
            )->execute([
                        'id' => $attr['id'],
                        'name' => $attr['name'],
                        'type' => $attr['type']
                    ]);

            foreach ($attr['items'] ?? [] as $index => $item) {

                $this->pdo->prepare(
                    'INSERT IGNORE INTO product_attributes
                     (id, product_id, attribute_id, displayValue, value, sort_order)
                     VALUES (:id, :product_id, :attribute_id, :displayValue, :value, :sort_order)'
                )->execute([
                            'id' => $prod['id'] . '-' . $attr['id'] . '-' . $item['id'],
                            'product_id' => $prod['id'],
                            'attribute_id' => $attr['id'],
                            'displayValue' => $item['displayValue'],
                            'value' => $item['value'],
                            'sort_order' => $index
                        ]);
            }
        }
    }
}