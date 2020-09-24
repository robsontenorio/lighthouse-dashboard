<?php

namespace Tests\Feature;

use Database\Factories\ClientFactory;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Traits\OperationAssertions;

class OperationsTest extends TestCase
{
    use OperationAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');
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

    public function test_slow_operations()
    {
        $this->customGraphQLRequest()
            ->times(5)
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

        // set "categories" operation slow
        $this->getOperationByName('categories')->requests()->update([
            'duration' => 999999999
        ]);

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("slowlestOperations", 2)
            ->assertPropValue("slowlestOperations", function ($data) {
                $this->assertEquals($data[0]['field']['name'], 'categories');
                $this->assertEquals($data[1]['field']['name'], 'products');
            });
    }
}
