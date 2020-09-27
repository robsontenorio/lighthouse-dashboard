<?php

namespace Tests\Feature;

use App\Models\Client;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class WelcomeStatisticsTest extends TestCase
{
    private Collection $clients;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        ProductFactory::times(5)->create();

        $this->clients = new Collection();
        $this->clients->push(Client::first()); // anonymous

        $marina = ClientFactory::new()->create(['username' => 'marina']);
        $giovanna = ClientFactory::new()->create(['username' => 'giovanna']);
        $amanda = ClientFactory::new()->create(['username' => 'amanda']);

        $this->clients->push($marina, $giovanna, $amanda);
    }

    /**
     * @dataProvider dataset
     */
    public function test_operation_tracing($requests, $query, $expect)
    {
        foreach ($requests as $request) {
            $client = $this->clients->where('username', $request['client'])->first();

            $this->customGraphQLRequest()
                ->forClient($client)
                ->withDateTime($request['dateTime'])
                ->times($request['times'])
                ->query($request['query']);
        }

        $this->get("/lighthouse-dashboard/?{$query}")
            ->assertPropValue('client_series', function ($data) use ($expect) {
                $this->assertEqualsCanonicalizing($data, $expect['clients']);
            })
            ->assertPropValue('requests_series', function ($data) use ($expect) {
                $this->assertEqualsCanonicalizing($data, $expect['requests']);
            });
    }

    public function dataset()
    {
        return [
            // CASE 1
            [
                'requests' => [
                    [
                        "dateTime" => "2020-09-27",
                        "client" => 'anonymous',
                        "times" => 3,
                        "query"  => '{ categories { id name } }'
                    ],
                    [
                        "dateTime" => "2020-09-27",
                        "client" => 'marina',
                        "times" => 5,
                        "query"  => '{ products { id name } }'
                    ],
                    [
                        "dateTime" => "2020-09-26",
                        "client" => 'giovanna',
                        "times" => 11,
                        "query"  => '{ products { id name} }'
                    ],
                ],
                'query' => 'start_date=in custom range&range[]=2020-09-26&range[]=2020-09-27',
                'expect' => [
                    'clients' => [
                        [
                            'x' =>  'anonymous',
                            'y' => 3
                        ],
                        [
                            'x' => 'marina',
                            'y' => 5
                        ],
                        [
                            'x' => 'giovanna',
                            'y' => 11
                        ],
                        [
                            'x' => 'amanda',
                            'y' => 0
                        ]
                    ],
                    'requests' => [
                        [
                            'x' => '2020-09-26',
                            'y' => 11,
                        ],
                        [
                            'x' => '2020-09-27',
                            'y' => 8,
                        ]
                    ]

                ]
            ],
        ];
    }
}
