<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Field;
use App\Models\Operation;
use App\Models\Type;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class OperationTracingTest extends TestCase
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
    public function test_operation_tracing($requests, $path, $expect)
    {
        foreach ($requests as $request) {
            $this->customGraphQLRequest()
                ->times($request['times'])
                ->query($request['query']);
        }

        [$type_name, $field_name] = explode('.', $path);

        $type = Type::where('name', $type_name)->first();
        $field = Field::where(['name' => $field_name, 'type_id' => $type->id])->first();
        $operation = Operation::where('field_id', $field->id)->first();

        $this->get("/lighthouse-dashboard/operations/{$operation->id}")
            ->assertPropValue('operation', function ($operation) use ($expect) {
                $total_tracings = count($operation['tracings']);
                $this->assertEquals($total_tracings, $expect['total_tracings']);
            });
    }

    public function dataset()
    {
        return [
            // CASE 1
            [
                'requests' => [
                    [
                        "times" => 3,
                        "query"  => '{ categories { id name } }'
                    ],
                    [
                        "times" => 3,
                        "query"  => '{ products { id name } }'
                    ],
                    [
                        "times" => 4,
                        "query"  => '{ products { id name category { id name} } }'
                    ],
                ],
                'path' => 'Query.products',
                'expect' => [
                    'total_tracings' => 7
                ]
            ],
        ];
    }
}
