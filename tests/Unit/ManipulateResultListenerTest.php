<?php

namespace Tests\Unit;

use App\Actions\StoreMetrics;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ManipulateResultListenerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema.graphql');
    }

    public function test_introspection_queries_does_not_dispatch_store_metrics_job()
    {
        $this->introspect();

        Bus::assertNotDispatched(StoreMetrics::class);
    }

    public function test_it_dispatch_store_metrics_job()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');

        Bus::assertDispatched(StoreMetrics::class);
    }

    public function test_uses_anonymous_client_if_there_is_no_authenticated_user()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');

        Bus::assertDispatched(function (StoreMetrics $job) {
            return $job->client->username == 'anonymous';
        });
    }

    public function test_get_username_of_client_if_authenticated()
    {
        $user = new User();
        $user->username = 'marina';

        $this->actingAs($user)->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');

        Bus::assertDispatched(function (StoreMetrics $job) {
            return $job->client->username == 'marina';
        });
    }

    public function test_username_respects_configuration()
    {
        config(['lighthouse-dashboard.client_identifier' => 'nickname']);

        $user = new User();
        $user->nickname = 'amanda';

        $this->actingAs($user)->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ');

        Bus::assertDispatched(function (StoreMetrics $job) {
            return $job->client->username == 'amanda';
        });
    }

    public function test_silent_database_failure_when_bootstraping()
    {
        // Force dashboard settings to a not configured connection
        config(['lighthouse-dashboard.connection' => 'mysql']);

        $this->graphQL('
            {
                products{
                    id                    
                    name
                }
            }
        ')->assertJsonPath('errors', null);

        Bus::assertNotDispatched(StoreMetrics::class);
    }
}
