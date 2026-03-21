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
    private static ?ObjectType $queryType = null;
    private static ?ObjectType $mutationType = null;
    private static ?ObjectType $attributeType = null;
    private static ?ObjectType $attributeItemType = null;
    private static ?ObjectType $priceType = null;
    private static ?ObjectType $currencyType = null;
    private static ?ObjectType $productType = null;
    private static ?ObjectType $categoryType = null;
    private static ?ObjectType $orderResponseType = null;
    private static ?InputObjectType $orderAttributeInputType = null;
    private static ?InputObjectType $orderItemInputType = null;

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
        }
        catch (\Throwable $e) {
            http_response_code(500);

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

    private static function attributeType(): ObjectType
    {
        return self::$attributeType ??= new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => Type::nonNull(fn() => Type::string()),
                'name' => Type::string(),
                'type' => Type::string(),
                'items' => Type::listOf(self::attributeItemType()),
            ],
        ]);
    }

    private static function attributeItemType(): ObjectType
    {
        return self::$attributeItemType ??= new ObjectType([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => Type::string(),
                'displayValue' => Type::string(),
                'value' => Type::string(),
            ],
        ]);
    }

    private static function priceType(): ObjectType
    {
        return self::$priceType ??= new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => Type::float(),
                'currency' => self::currencyType(),
            ],
        ]);
    }

    private static function currencyType(): ObjectType
    {
        return self::$currencyType ??= new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'label' => Type::string(),
                'symbol' => Type::string(),
            ]
        ]);
    }

    private static function productType(): ObjectType
    {
        return self::$productType ??= new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(fn() => Type::string()),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'gallery' => Type::listOf(Type::string()),
                'description' => Type::string(),
                'category' => Type::string(),
                'brand' => Type::string(),
                'prices' => Type::listOf(self::priceType()),
                'attributes' => [
                    'type' => Type::listOf(self::attributeType()),
                    'resolve' => fn($product) => \App\Resolvers\AttributeResolver::resolveByProductId($product['id'])
                ],
            ],
        ]);
    }

    private static function categoryType(): ObjectType
    {
        return self::$categoryType ??= new ObjectType([
            'name' => 'Category',
            'fields' => [
                'id' => Type::string(),
                'name' => Type::string(),
            ],
        ]);
    }

    private static function queryType(): ObjectType
    {
        return self::$queryType ??= new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf(self::productType()),
                    'args' => [
                        'category' => Type::string(),
                    ],
                    'resolve' => fn($rootValue, $args) => ProductResolver::resolveAll($args['category'] ?? null)
                ],
                'product' => [
                    'type' => self::productType(),
                    'args' => [
                        'id' => Type::nonNull(fn() => Type::string()),
                    ],
                    'resolve' => fn($root, $args) => ProductResolver::resolveByID($args['id'])
                ],
                'categories' => [
                    'type' => Type::listOf(self::categoryType()),
                    'resolve' => fn() => CategoryResolver::resolveAll()
                ],
            ],
        ]);
    }

    private static function orderAttributeInputType(): InputObjectType
    {
        return self::$orderAttributeInputType ??= new InputObjectType([
            'name' => 'OrderAttributeInput',
            'fields' => [
                'id' => Type::nonNull(fn() => Type::string()),
                'value' => Type::nonNull(fn() => Type::string()),
            ]
        ]);
    }

    private static function orderItemInputType(): InputObjectType
    {
        return self::$orderItemInputType ??= new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => Type::nonNull(fn() => Type::string()),
                'quantity' => Type::nonNull(fn() => Type::int()),
                'price_amount' => Type::nonNull(fn() => Type::float()),
                'currency_label' => Type::nonNull(fn() => Type::string()),
                'currency_symbol' => Type::nonNull(fn() => Type::string()),
                'attributes' => Type::listOf(fn() => self::orderAttributeInputType()),
            ]
        ]);
    }

    private static function orderResponseType(): ObjectType
    {
        return self::$orderResponseType ??= new ObjectType([
            'name' => 'OrderResponse',
            'fields' => [
                'id' => Type::string(),
                'message' => Type::string(),
            ]
        ]);
    }

    private static function mutationType(): ObjectType
    {
        return self::$mutationType ??= new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => self::orderResponseType(),
                    'args' => [
                        'items' => Type::nonNull(fn() => Type::listOf(fn() => Type::nonNull(fn() => self::orderItemInputType()))),
                    ],
                    'resolve' => fn($root, $args) => OrderResolver::createOrder($args['items'])
                ]
            ]
        ]);
    }
}