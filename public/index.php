<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


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

use App\Controller\GraphQL;

GraphQL::handle();