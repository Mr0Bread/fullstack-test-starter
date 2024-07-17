<?php

echo "resolvers.php is being executed";
exit;

require 'vendor/autoload.php';
require 'database.php';

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Error\Debug;

$categoryType = new ObjectType([
    'name' => 'Category',
    'fields' => [
        'id' => Type::id(),
        'name' => Type::string(),
    ],
]);

$productType = new ObjectType([
    'name' => 'Product',
    'fields' => [
        'id' => Type::id(),
        'name' => Type::string(),
        'description' => Type::string(),
        'price' => Type::float(),
        'category' => Type::string(),
        'in_stock' => Type::boolean(),
        'brand' => Type::string(),
    ],
]);

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'categories' => [
            'type' => Type::listOf($categoryType),
            'resolve' => function() {
                return Database::getAllCategories();
            },
        ],
        'products' => [
            'type' => Type::listOf($productType),
            'resolve' => function() {
                return Database::getAllProducts();
            },
        ],
        'product' => [
            'type' => $productType,
            'args' => [
                'id' => Type::id(),
            ],
            'resolve' => function($root, $args) {
                return Database::getProductById($args['id']);
            },
        ],
    ],
]);

$schema = new Schema([
    'query' => $queryType,
]);

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$query = $input['query'];
$variableValues = isset($input['variables']) ? $input['variables'] : null;

$rootValue = [];
$context = [];
$debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;

$result = GraphQL::executeQuery($schema, $query, $rootValue, $context, $variableValues);
$output = $result->toArray($debug);
header('Content-Type: application/json');
echo json_encode($output);
