<?php

namespace Tests\Feature;

use App\Models\Client;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;
use Tests\Utils\Models\Product;
use Tests\Utils\Traits\OperationAssertions;

class OperationsTopTest extends TestCase
{
    use OperationAssertions;

    private Collection $clients;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        ProductFactory::times(5)->create();

        $this->clients = new Collection();
        $this->clients->push(Client::first()); // anonymous

        $moreClients = ClientFactory::times(3)->create();
        $moreClients->each(fn ($client) => $this->clients->push($client));
    }

    public function test_top_operations()
    {
        $this->customGraphQLRequest()
            ->times(3)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $this->customGraphQLRequest()
            ->times(5)
            ->query('
                {
                    categories{
                        id
                        name
                    }
                }
            ');

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("topOperations", 2)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 5);
                $this->assertEquals($data[0]['field']['name'], 'categories');

                $this->assertEquals($data[1]['total_requests'], 3);
                $this->assertEquals($data[1]['field']['name'], 'products');
            });
    }

    /**
     * @dataProvider dataset
     */
    public function test_top_operations_with_filters($requests, $query_string, $expect)
    {
        foreach ($requests as $request) {
            $client = $this->clients->where('id', $request['client_id'])->first();

            $this->customGraphQLRequest()
                ->withDateTime($request['dateTime'])
                ->forClient($client)
                ->times($request['times'])
                ->query($request['query']);
        }

        $this->get("/lighthouse-dashboard/operations?{$query_string}")
            ->assertPropCount("topOperations", $expect['total_operations'])
            ->assertPropValue("topOperations", function ($data) use ($expect) {
                foreach ($data as $key => $data) {
                    $this->assertEquals($data['field']['name'], $expect['operations'][$key]['name']);
                    $this->assertEquals($data['total_requests'], $expect['operations'][$key]['total_requests']);
                }
            });
    }

    public function dataset()
    {
        return [
            // CASE 1
            [
                'requests' => [
                    [
                        "dateTime" => "today",
                        "client_id" => 1,  // "1" is anonymous
                        "times" => 3,
                        "query"  => '{ categories {id name} }'
                    ],
                    [
                        "dateTime" => "yesterday",
                        "client_id" => 2,
                        "times" => 4,
                        "query"  => '{ products {id name} }'
                    ],
                ],
                'query_string' => "start_date=yesterday",
                'expect' => [
                    'total_operations' => 2,
                    'operations' => [
                        [
                            'name' => 'products',
                            'total_requests' => 4
                        ],
                        [
                            'name' => 'categories',
                            'total_requests' => 3
                        ],
                    ]
                ]
            ],
            // CASE 2
            [
                'requests' => [
                    [
                        "dateTime" => "today",
                        "client_id" => 1,  // "1" is anonymous
                        "times" => 3,
                        "query"  => '{ categories {id name} }'
                    ],
                    [
                        "dateTime" => "yesterday",
                        "client_id" => 1,
                        "times" => 4,
                        "query"  => '{ products {id name} }'
                    ],
                ],
                'query_string' => "start_date=today",
                'expect' => [
                    'total_operations' => 2,
                    'operations' => [
                        [
                            'name' => 'categories',
                            'total_requests' => 3
                        ],
                        [
                            'name' => 'products',
                            'total_requests' => 0
                        ],
                    ]
                ]
            ],
            // CASE 3
            [
                'requests' => [
                    [
                        "dateTime" => "today",
                        "client_id" => 1,
                        "times" => 3,
                        "query"  => '{ categories {id name} }'
                    ],
                    [
                        "dateTime" => "yesterday",
                        "client_id" => 2,
                        "times" => 4,
                        "query"  => '{ products {id name} }'
                    ],
                    [
                        "dateTime" => "-5 days",
                        "client_id" => 1,
                        "times" => 4,
                        "query"  => '{ products {id name} }'
                    ],
                ],
                'query_string' => "start_date=last week&clients[]=2",
                'expect' => [
                    'total_operations' => 2,
                    'operations' => [
                        [
                            'name' => 'products',
                            'total_requests' => 4
                        ],
                        [
                            'name' => 'categories',
                            'total_requests' => 0
                        ],
                    ]
                ]
            ]
        ];
    }
}
