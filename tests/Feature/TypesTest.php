<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Type;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class TypesTest extends TestCase
{
    private Collection $clients;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        ProductFactory::times(3)->create();

        $this->clients = new Collection();
        $this->clients->push(Client::first()); // anonymous

        $moreClients = ClientFactory::times(3)->create();
        $moreClients->each(fn ($client) => $this->clients->push($client));
    }

    /**
     * @dataProvider dataset
     */
    public function test_types_with_filters($requests, $query_string, $expect)
    {
        foreach ($requests as $request) {
            $client = $this->clients->where('id', $request['client_id'])->first();

            $this->customGraphQLRequest()
                ->withDateTime($request['dateTime'])
                ->forClient($client)
                ->times($request['times'])
                ->query($request['query']);
        }

        $total_types = Type::count();

        $this->get("/lighthouse-dashboard/types?{$query_string}")
            ->assertPropCount("types", $total_types)
            ->assertPropValue("types", function ($types) use ($expect) {

                $types = collect($types)->pluck('fields', 'name')->map(function ($item) {
                    return collect($item)->pluck('total_requests', 'name');
                })->toArray();

                $this->assertEqualsCanonicalizing($types, $expect['types']);
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
                    'types' => [
                        'Query' =>  [
                            'products' => 4,
                            'categories' => 3
                        ],
                        'Product' => [
                            'id' => 4,
                            'name' => 4,
                            'color' => 0,
                            'category' => 0
                        ],
                        'Color' => [
                            'id' => 0,
                            'name' => 0
                        ],
                        'Category' => [
                            'id' => 3,
                            'name' => 3
                        ]
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
                        "client_id" => 2,
                        "times" => 4,
                        "query"  => '{ products {id name color { id name } } }'
                    ],
                ],
                'query_string' => "start_date=yesterday",
                'expect' => [
                    'types' => [
                        'Query' =>  [
                            'products' => 4,
                            'categories' => 3
                        ],
                        'Product' => [
                            'id' => 4,
                            'name' => 4,
                            'color' => 4,
                            'category' => 0
                        ],
                        'Color' => [
                            'id' => 4,
                            'name' => 4
                        ],
                        'Category' => [
                            'id' => 3,
                            'name' => 3
                        ]
                    ]
                ]
            ],

        ];
    }
}
