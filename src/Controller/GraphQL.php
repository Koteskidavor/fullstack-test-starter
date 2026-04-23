<?php
declare(strict_types=1);

namespace App\Controller;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL as GraphQLBase;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Attribute;
use App\Models\Price;

use App\Resolvers\ProductResolver;
use App\Resolvers\CategoryResolver;
use App\Resolvers\OrderResolver;
use App\Resolvers\AttributeResolver;

final class GraphQL
{
    private ?ObjectType $queryType = null;
    private ?ObjectType $mutationType = null;
    private ?ObjectType $attributeType = null;
    private ?ObjectType $attributeItemType = null;
    private ?ObjectType $priceType = null;
    private ?ObjectType $currencyType = null;
    private ?ObjectType $productType = null;
    private ?ObjectType $categoryType = null;
    private ?ObjectType $orderResponseType = null;
    private ?InputObjectType $orderAttributeInputType = null;
    private ?InputObjectType $orderItemInputType = null;

    private ?ProductResolver $productResolver = null;
    private ?CategoryResolver $categoryResolver = null;
    private ?OrderResolver $orderResolver = null;
    private ?AttributeResolver $attributeResolver = null;

    public function __construct(
        ProductResolver $productResolver,
        CategoryResolver $categoryResolver,
        OrderResolver $orderResolver,
        AttributeResolver $attributeResolver,
    ) {
        $this->productResolver = $productResolver;
        $this->categoryResolver = $categoryResolver;
        $this->orderResolver = $orderResolver;
        $this->attributeResolver = $attributeResolver;
    }

    public function handle(): void
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
                'query' => $this->queryType(),
                'mutation' => $this->mutationType(),
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

    private function productResolver(): ProductResolver
    {
        return $this->productResolver;
    }
    private function categoryResolver(): CategoryResolver
    {
        return $this->categoryResolver;
    }
    private function orderResolver(): OrderResolver
    {
        return $this->orderResolver;
    }
    private function attributeResolver(): AttributeResolver
    {
        return $this->attributeResolver;
    }
    private function attributeType(): ObjectType
    {
        return $this->attributeType ??= new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn(Attribute $attribute) => $attribute->getId()
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn(Attribute $attribute) => $attribute->getName()
                ],
                'type' => [
                    'type' => Type::string(),
                    'resolve' => fn(Attribute $attribute) => $attribute->getType()
                ],
                'items' => [
                    'type' => Type::listOf($this->attributeItemType()),
                    'resolve' => fn(Attribute $attribute) => $attribute->getItems()
                ],
            ],
        ]);
    }

    private function attributeItemType(): ObjectType
    {
        return $this->attributeItemType ??= new ObjectType([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => Type::string(),
                'displayValue' => Type::string(),
                'value' => Type::string(),
            ],
        ]);
    }

    private function priceType(): ObjectType
    {
        return $this->priceType ??= new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => [
                    'type' => Type::float(),
                    'resolve' => fn(Price $price) => $price->getAmount()
                ],
                'currency' => [
                    'type' => $this->currencyType(),
                    'resolve' => fn(Price $price) => $price->getCurrency()
                ],
            ],
        ]);
    }

    private function currencyType(): ObjectType
    {
        return $this->currencyType ??= new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'label' => [
                    'type' => Type::string(),
                    'resolve' => fn(array $currencyData) => $currencyData['label'] ?? null
                ],
                'symbol' => [
                    'type' => Type::string(),
                    'resolve' => fn(array $currencyData) => $currencyData['symbol'] ?? null
                ],
            ],
        ]);
    }

    private function productType(): ObjectType
    {
        return $this->productType ??= new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn(Product $product) => $product->getId()
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn(Product $product) => $product->getName()
                ],
                'inStock' => [
                    'type' => Type::boolean(),
                    'resolve' => fn(Product $product) => $product->getInStock()
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => fn(Product $product) => $product->getGallery()
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => fn(Product $product) => $product->getDescription()
                ],
                'category' => [
                    'type' => Type::string(),
                    'resolve' => fn(Product $product) => $product->getCategory()
                ],
                'brand' => [
                    'type' => Type::string(),
                    'resolve' => fn(Product $product) => $product->getBrand()
                ],
                'prices' => [
                    'type' => Type::listOf($this->priceType()),
                    'resolve' => fn(Product $product) => $this->productResolver()->resolvePrices($product->getId())
                ],
                'attributes' => [
                    'type' => Type::listOf($this->attributeType()),
                    'resolve' => fn(Product $product) => $this->attributeResolver()->resolveByProductId($product->getId())
                ],
            ],
        ]);
    }

    private function categoryType(): ObjectType
    {
        return $this->categoryType ??= new ObjectType([
            'name' => 'Category',
            'fields' => [
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn(Category $category) => $category->getName()
                ],
            ],
        ]);
    }

    private function queryType(): ObjectType
    {
        return $this->queryType ??= new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf($this->productType()),
                    'args' => [
                        'category' => Type::string(),
                    ],
                    'resolve' => fn($rootValue, $args) => $this->productResolver()->resolveAll($args['category'] ?? null)
                ],
                'product' => [
                    'type' => $this->productType(),
                    'args' => [
                        'id' => Type::nonNull(fn() => Type::string()),
                    ],
                    'resolve' => fn($root, $args) => $this->productResolver()->resolveById($args['id'])
                ],
                'categories' => [
                    'type' => Type::listOf($this->categoryType()),
                    'resolve' => fn() => $this->categoryResolver()->resolveAll()
                ],
            ],
        ]);
    }

    private function orderAttributeInputType(): InputObjectType
    {
        return $this->orderAttributeInputType ??= new InputObjectType([
            'name' => 'OrderAttributeInput',
            'fields' => [
                'id' => Type::nonNull(fn() => Type::string()),
                'value' => Type::nonNull(fn() => Type::string()),
            ]
        ]);
    }

    private function orderItemInputType(): InputObjectType
    {
        return $this->orderItemInputType ??= new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => Type::nonNull(fn() => Type::string()),
                'quantity' => Type::nonNull(fn() => Type::int()),
                'price_amount' => Type::nonNull(fn() => Type::float()),
                'currency_label' => Type::nonNull(fn() => Type::string()),
                'currency_symbol' => Type::nonNull(fn() => Type::string()),
                'attributes' => Type::listOf(fn() => $this->orderAttributeInputType()),
            ]
        ]);
    }

    private function orderResponseType(): ObjectType
    {
        return $this->orderResponseType ??= new ObjectType([
            'name' => 'OrderResponse',
            'fields' => [
                'id' => [
                    'type' => Type::string(),
                    'resolve' => fn(Order $order) => (string) $order->getId()
                ],
                'message' => [
                    'type' => Type::string(),
                    'resolve' => fn(Order $order) => $order->getMessage()
                ],
            ]
        ]);
    }

    private function mutationType(): ObjectType
    {
        return $this->mutationType ??= new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => $this->orderResponseType(),
                    'args' => [
                        'items' => Type::nonNull(fn() => Type::listOf(fn() => Type::nonNull(fn() => $this->orderItemInputType()))),
                    ],
                    'resolve' => fn($root, $args) => $this->orderResolver()->createOrder($args['items'])
                ]
            ]
        ]);
    }
}