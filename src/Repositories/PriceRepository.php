<?php
declare(strict_types=1);
namespace App\Repositories;
use PDO;
use App\Models\Price;

class PriceRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function findByProductId(string $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.amount, p.currency as label, COALESCE(c.symbol, '$') as symbol
            FROM prices p
            LEFT JOIN currencies c ON p.currency = c.label
            WHERE p.product_id = ?
        ");

        $stmt->execute([$productId]);
        $priceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            $currencyData = [
                'label' => $row['label'],
                'symbol' => $row['symbol']
            ];
            return new Price((float) $row['amount'], $currencyData);
        }, $priceData);
    }
}