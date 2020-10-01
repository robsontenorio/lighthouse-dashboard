<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class OperationWithErrorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        ProductFactory::times(5)->create();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-with-internal-error.graphql');
    }

    public function test_node_with_internal_error()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                    badges {
                        name
                    }
                }
            }
        ');

        $this->get("/lighthouse-dashboard/errors")
            ->assertPropCount("errors", 1)
            ->assertPropValue("errors", function ($data) {
                $this->assertEquals('internal', $data[0]['category']);
            });
    }

    public function test_root_with_internal_error()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                    person {
                        name
                    }
                }
            }
        ');

        $this->get("/lighthouse-dashboard/errors")
            ->assertPropCount("errors", 1)
            ->assertPropValue("errors", function ($data) {
                $this->assertEquals('internal', $data[0]['category']);
            });
    }
}
