<?php

namespace Tests\Unit;

use App\Models\Type;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Traits\TypeAssertions;

class SyncGraphQLSchemaTest extends TestCase
{
    use TypeAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema.graphql');

        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');
    }

    public function test_create_schema_on_first_request()
    {
        $this->assertEquals(Type::count(), 3);

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
    }

    public function test_does_not_sync_schema_again_if_nothing_has_changed()
    {
        $this->markTestIncomplete();
    }

    public function test_update_schema_after_new_request_if_it_has_changed()
    {
        // TODO not working because cant change schema on same test method
        $this->markTestSkipped();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        $this->rebuildTestSchema();

        $response = $this->graphQL('
            {
                categories{
                    id
                    name
                }
            }
        ');

        $this->assertEquals(Type::count(), 4);

        $this->assertHasType([
            'name' => 'Query',
        ])->withFields([
            [
                'name' => 'products',
                'description' => 'List all products',
                'type_def' => '[Product]'
            ],
            [
                'name' => 'categories',
                'description' => 'List all categories',
                'type_def' => '[Category]'
            ]
        ]);

        $this->assertHasType([
            'name' => 'Category',
            'description' => 'A category',
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
            'description' => 'Our new secret product',
        ])->withFields([
            [
                'name' => 'id',
                'description' => null,
                'type_def' => 'ID!'
            ],
            [
                'name' => 'name',
                'description' => 'The greate name of product',
                'type_def' => 'String!'
            ],
            [
                'name' => 'color',
                'description' => null,
                'type_def' => 'Color!'
            ],
            [
                'name' => 'category',
                'description' => null,
                'type_def' => 'Category!'
            ],
        ]);
    }
}
