<?php

namespace Tests\Feature;

use App\Models\Operation;
use App\Models\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;
use Tests\Utils\Traits\OperationAssertions;

class OperationsTest extends TestCase
{
    use OperationAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        ProductFactory::new()->create();
    }

    public function test_top_operations()
    {
        $this->graphQLTimes(3, '
            {
                products{
                    id
                    name
                }
            }
        ');

        $this->graphQLTimes(5, '
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
}
