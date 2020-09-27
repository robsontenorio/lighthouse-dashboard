<?php

namespace Tests\Feature;

use Database\Factories\ClientFactory;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OperationsWithClientFilterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');
    }

    public function test_top_operations_with_clients_filters()
    {
        // Anonymous
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

        $client1 = ClientFactory::new()->create();
        $client2 = ClientFactory::new()->create();

        $this->customGraphQLRequest()
            ->times(3)
            ->forClient($client1)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $this->customGraphQLRequest()
            ->times(3)
            ->forClient($client2)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 9);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });

        $this->get("/lighthouse-dashboard/operations?clients[]={$client1->id}")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 3);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });

        $this->get("/lighthouse-dashboard/operations?clients[]={$client1->id}&clients[]={$client2->id}")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 6);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });

        // Client ID = 1 anonymous
        $this->get("/lighthouse-dashboard/operations?clients[]=1")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 3);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }
}
