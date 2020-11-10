<?php

namespace Tests\Feature;

use Database\Factories\ClientFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class IgnoreClientsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');
    }

    public function test_ignore_clients()
    {
        config(['lighthouse-dashboard.ignore_clients' => ['test-client', 'anonymous']]);

        $client1 = new User();
        $client1->username = 'sales-client';

        $client2 = new User();
        $client2->username = 'test-client';

        $this->actingAs($client1)->graphQL('
            {
                products{
                    id
                    name                  
                }
            }
        ');

        $this->actingAs($client2)->graphQL('
            {
                products{
                    id
                    name                  
                }
            }
        ');

        Auth::logout();

        // Anonymous
        $this->graphQL('
            {
                products{
                    id
                    name                  
                }
            }
        ');

        $this->get("/lighthouse-dashboard/errors")->assertPropCount("errors", 0);

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 1);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }
}
