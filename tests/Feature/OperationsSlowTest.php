<?php

namespace Tests\Feature;

use App\Models\Client;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OperationsSlowTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        $this->clients = new Collection();
        $this->clients->push(Client::first()); // anonymous

        $moreClients = ClientFactory::times(3)->create();
        $moreClients->each(fn ($client) => $this->clients->push($client));
    }

    /**
     * @dataProvider dataset
     */
    public function test_slow_with_filter($requests, $query_string, $expect)
    {
        foreach ($requests as $request) {
            $this->customGraphQLRequest()
                ->withDateTime($request['dateTime'])
                ->withDuration($request['duration'])
                ->times($request['times'])
                ->query($request['query']);
        }

        $this->get("/lighthouse-dashboard/operations?{$query_string}")
            ->assertPropCount("slowlestOperations", $expect['total_operations'])
            ->assertPropValue("slowlestOperations", function ($data) use ($expect) {
                foreach ($data as $key => $data) {
                    $this->assertEquals($data['field']['name'], $expect['operations'][$key]['name']);
                    $this->assertEquals($data['average_duration'], $expect['operations'][$key]['average_duration']);
                    $this->assertEquals($data['latest_duration'], $expect['operations'][$key]['latest_duration']);
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
                        "dateTime" => "yesterday",
                        "times" => 2,
                        "duration" => 2000000,
                        "query"  => '{ products {id name} }'
                    ],
                    [
                        "dateTime" => "today",
                        "times" => 7,
                        "duration" => 1000000,
                        "query"  => '{ products {id name} }'
                    ],
                    [
                        "dateTime" => "today",
                        "times" => 7,
                        "duration" => 1000000,
                        "query"  => '{ categories {id name} }'
                    ],
                ],
                'query_string' => "start_date=yesterday",
                'expect' => [
                    'total_operations' => 2,
                    'operations' => [
                        [
                            'name' => 'products',
                            'average_duration' => 1222222,
                            'latest_duration' => 1000000
                        ],
                        [
                            'name' => 'categories',
                            'average_duration' => 1000000,
                            'latest_duration' => 1000000
                        ],
                    ]
                ]
            ],
            // CASE 1
            [
                'requests' => [
                    [
                        "dateTime" => "yesterday",
                        "times" => 2,
                        "client_id" => 2,
                        "duration" => 2000000,
                        "query"  => '{ products {id name} }'
                    ],
                    [
                        "dateTime" => "today",
                        "times" => 7,
                        "client_id" => 1,
                        "duration" => 3000000,
                        "query"  => '{ products {id name} }'
                    ],
                    [
                        "dateTime" => "today",
                        "times" => 7,
                        "client_id" => 1,
                        "duration" => 1000000,
                        "query"  => '{ categories {id name} }'
                    ],
                ],
                'query_string' => "start_date=today&clients[]=1",
                'expect' => [
                    'total_operations' => 2,
                    'operations' => [
                        [
                            'name' => 'products',
                            'average_duration' => 3000000,
                            'latest_duration' => 3000000
                        ],
                        [
                            'name' => 'categories',
                            'average_duration' => 1000000,
                            'latest_duration' => 1000000
                        ],
                    ]
                ]
            ],
        ];
    }
}
