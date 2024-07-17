namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() {
        try {
            // Define the Product Type
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::id())],
                    'name' => ['type' => Type::string()],
                    'description' => ['type' => Type::string()],
                    'price' => ['type' => Type::float()],
                    'category' => ['type' => Type::string()],
                    'in_stock' => ['type' => Type::boolean()],
                    'brand' => ['type' => Type::string()],
                ],
            ]);

            // Define the Query Type
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'echo' => [
                        'type' => Type::string(),
                        'args' => [
                            'message' => ['type' => Type::string()],
                        ],
                        'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function() {
                            // Replace this with your actual data fetching logic
                            return [
                                [
                                    'id' => '1',
                                    'name' => 'Product 1',
                                    'description' => 'Description 1',
                                    'price' => 99.99,
                                    'category' => 'Category 1',
                                    'in_stock' => true,
                                    'brand' => 'Brand 1'
                                ],
                                [
                                    'id' => '2',
                                    'name' => 'Product 2',
                                    'description' => 'Description 2',
                                    'price' => 199.99,
                                    'category' => 'Category 2',
                                    'in_stock' => false,
                                    'brand' => 'Brand 2'
                                ]
                            ];
                        }
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                ],
            ]);

            // Create the Schema
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
