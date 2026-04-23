<?php
declare(strict_types=1);

use App\Controller\GraphQL;
use App\Database;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\AttributeRepository;
use App\Repositories\PriceRepository;
use App\Resolvers\ProductResolver;
use App\Resolvers\CategoryResolver;
use App\Resolvers\OrderResolver;
use App\Resolvers\AttributeResolver;
use App\Resolvers\PriceResolver;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'message' => 'GraphQL API endpoint',
        'usage' => 'Send a POST request with a JSON body containing a "query" field.',
        'example' => [
            'query' => '{ products { id name } }'
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = Database::getConnection();

$productRepository = new ProductRepository($pdo);
$categoryRepository = new CategoryRepository($pdo);
$orderRepository = new OrderRepository($pdo);
$attributeRepository = new AttributeRepository($pdo);
$priceRepository = new PriceRepository($pdo);

$priceResolver = new PriceResolver($priceRepository);
$productResolver = new ProductResolver($productRepository, $priceResolver);
$categoryResolver = new CategoryResolver($categoryRepository);
$orderResolver = new OrderResolver($orderRepository);
$attributeResolver = new AttributeResolver($attributeRepository);

$graphqlController = new GraphQL($productResolver, $categoryResolver, $orderResolver, $attributeResolver);

$graphqlController->handle();