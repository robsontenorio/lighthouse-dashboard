<?php

namespace Tests\Feature;

use App\Models\Client;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Traits\OperationAssertions;

class OperationSumaryTest extends TestCase
{
    use OperationAssertions;

    private Collection $clients;

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
    public function test_operation_sumary_filters($requests, $operation_id, $query_string, $expect)
    {
        foreach ($requests as $request) {
            $client = $this->clients->where('id', $request['client_id'])->first();

            $this->customGraphQLRequest()
                ->withDateTime($request['dateTime'])
                ->forClient($client)
                ->times($request['times'])
                ->query($request['query']);
        }

        $clients = $this->get("/lighthouse-dashboard/operations/{$operation_id}/sumary?{$query_string}")->json();

        foreach ($clients as $key => $client) {
            $this->assertEquals($client['id'], $expect['clients'][$key]['id']);
            $this->assertEquals($client['total_requests'], $expect['clients'][$key]['total_requests']);
        }
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
                        "query"  => '{ categories {id name} }'
                    ],
                ],
                'operation_id' => 1,
                'query_string' => "start_date=yesterday",
                'expect' => [
                    'clients' => [
                        [
                            'id' => 1,
                            'total_requests' => 3
                        ],
                        [
                            'id' => 2,
                            'total_requests' => 4
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
                        "query"  => '{ categories {id name} }'
                    ],
                    [
                        "dateTime" => "yesterday",
                        "client_id" => 3,
                        "times" => 5,
                        "query"  => '{ categories {id name} }'
                    ],
                ],
                'operation_id' => 1,
                'query_string' => "start_date=yesterday&clients[]=3",
                'expect' => [
                    'clients' => [
                        [
                            'id' => 3,
                            'total_requests' => 5
                        ]
                    ]
                ]
            ],
        ];
    }
}
