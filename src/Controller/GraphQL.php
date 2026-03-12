<?php
declare(strict_types=1);

namespace App\Controller;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL as GraphQLBase;
use App\Resolvers\ProductResolver;
use App\Resolvers\CategoryResolver;
use App\Resolvers\OrderResolver;

final class GraphQL
{
    public static function handle(): void
    {
        header('Content-Type: application/json');

        try {
            $rawInput = file_get_contents('php://input');

            if ($rawInput === false || trim($rawInput) === '') {
                http_response_code(400);
                echo json_encode(['errors' => [['message' => 'Request body is empty.']]]);
                return;
            }

            $input = json_decode($rawInput, true);

            if (!is_array($input)) {
                http_response_code(400);
                echo json_encode(['errors' => [['message' => 'Invalid JSON in request body.']]]);
                return;
            }

            $query = $input['query'] ?? null;

            if (!is_string($query) || trim($query) === '') {
                http_response_code(400);
                echo json_encode(['errors' => [['message' => 'Missing or empty "query" field in request.']]]);
                return;
            }

            $variables = $input['variables'] ?? null;

            $schema = new Schema([
                'query' => self::queryType(),
                'mutation' => self::mutationType(),
            ]);

            $result = GraphQLBase::executeQuery(
                $schema,
                $query,
                null,
                null,
                is_array($variables) ? $variables : null
            );

            echo json_encode($result->toArray());
        } catch (\Throwable $e) {
            http_response_code(500);

            error_log("GraphQL Exception: " . $e->getMessage());
            error_log("Stack: " . $e->getTraceAsString());

            echo json_encode([
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                ]
            ]);
        }
    }

    private static function queryType(): ObjectType
    {
        $attributeType = new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'type' => Type::string(),
                'items' => Type::listOf(new ObjectType([
                    'name' => 'AttributeItem',
                    'fields' => [
                        'id' => Type::string(),
                        'displayValue' => Type::string(),
                        'value' => Type::string(),
                    ],
                ])),
            ],
        ]);

        $priceType = new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => Type::float(),
                'currency' => new ObjectType([
                    'name' => 'Currency',
                    'fields' => [
                        'label' => Type::string(),
                        'symbol' => Type::string(),
                    ]
                ]),
            ],
        ]);

        $productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'gallery' => Type::listOf(Type::string()),
                'description' => Type::string(),
                'category' => Type::string(),
                'brand' => Type::string(),
                'prices' => Type::listOf($priceType),
                'attributes' => Type::listOf($attributeType),
            ],
        ]);

        $categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'id' => Type::string(),
                'name' => Type::string(),
            ],
        ]);

        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf($productType),
                    'resolve' => fn() => ProductResolver::resolveAll()
                ],
                'product' => [
                    'type' => $productType,
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) => ProductResolver::resolveByID($args['id'])
                ],
                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => fn() => CategoryResolver::resolveAll()
                ],
            ],
        ]);
    }

    private static function orderAttributeInputType(): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderAttributeInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
            ]
        ]);
    }

    private static function orderItemInputType(): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'price_amount' => Type::nonNull(Type::float()),
                'currency_label' => Type::nonNull(Type::string()),
                'currency_symbol' => Type::nonNull(Type::string()),
                'attributes' => Type::listOf(self::orderAttributeInputType()),
            ]
        ]);
    }
    private static function mutationType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => new ObjectType([
                        'name' => 'OrderResponse',
                        'fields' => [
                            'id' => Type::string(),
                            'message' => Type::string(),
                        ]
                    ]),
                    'args' => [
                        'items' => Type::nonNull(Type::listOf(Type::nonNull(self::orderItemInputType()))),
                    ],
                    'resolve' => fn($root, $args) => OrderResolver::createOrder($args['items'])
                ]
            ]
        ]);
    }
}