<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Field;
use App\Models\Type;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class FieldSumaryTest extends TestCase
{
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

    /**
     * @dataProvider dataset
     */
    public function test_field_sumary_filters($requests, $path, $query_string, $expect)
    {
        foreach ($requests as $request) {
            $client = $this->clients->where('id', $request['client_id'])->first();

            $this->customGraphQLRequest()
                ->withDateTime($request['dateTime'])
                ->forClient($client)
                ->times($request['times'])
                ->query($request['query']);
        }

        [$type_name, $field_name] = explode('.', $path);

        $type = Type::where('name', $type_name)->first();
        $field = Field::where(['name' => $field_name, 'type_id' => $type->id])->first();

        $clients = $this->get("/lighthouse-dashboard/fields/{$field->id}/sumary?{$query_string}")->json();

        $clients = collect($clients)->pluck('metrics', 'id')->map(function ($item) {
            return collect($item)->pluck('total_requests', 'field.name');
        })->toArray();

        $this->assertEquals($clients, $expect['clients']);
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
                        "query"  => '{ categories { id name } }'
                    ],
                    [
                        "dateTime" => "today",
                        "client_id" => 2,
                        "times" => 3,
                        "query"  => '{ products { id name } }'
                    ],
                    [
                        "dateTime" => "yesterday",
                        "client_id" => 1,
                        "times" => 4,
                        "query"  => '{ products { id name category { id name} } }'
                    ],
                ],
                'path' => 'Category.name',
                'query_string' => "start_date=yesterday",
                'expect' => [
                    'clients' => [
                        1 => [
                            'categories' => 3,
                            'products' => 4
                        ],
                    ]
                ]
            ],
        ];
    }
}
