<?php

namespace Tests\Unit;

use App\Models\Type;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;

class SyncGraphQLSchemaTest extends TestCase
{
    /**
     * Always make a request before each scenario.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema.graphql');

        ProductFactory::new()->create();
    }

    public function test_create_schema_on_first_request()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');

        $this->assertHasType([
            'name' => 'Query',
        ])->withFields([
            [
                'name' => 'products',
                'description' => 'List all products',
                'type_def' => '[Product]'
            ]
        ]);

        $this->assertHasType([
            'name' => 'Color',
            'description' => 'A beautiful color',
        ])->withFields([
            [
                'name' => 'id',
                'description' => null,
                'type_def' => 'ID!'
            ],
            [
                'name' => 'name',
                'description' => null,
                'type_def' => 'String!'
            ],
        ]);

        $this->assertHasType([
            'name' => 'Product',
            'description' => 'Our secret product',
        ])->withFields([
            [
                'name' => 'id',
                'description' => null,
                'type_def' => 'ID!'
            ],
            [
                'name' => 'name',
                'description' => 'The name of product',
                'type_def' => 'String!'
            ],
            [
                'name' => 'color',
                'description' => null,
                'type_def' => 'Color'
            ],
        ]);

        $this->assertEquals(Type::count(), 3);
    }

    public function test_does_not_sync_schema_again_if_nothing_has_changed()
    {
        $this->markTestIncomplete();
    }

    public function test_update_schema_after_new_request_if_it_has_changed()
    {
        $this->markTestIncomplete();
    }
}
