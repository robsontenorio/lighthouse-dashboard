<?php

namespace Tests;

use App\Providers\LighthouseDashboardServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\TestSchemaProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Nuwave\Lighthouse\Tracing\TracingServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Tests\Utils\Traits\InertiaAssertions;

/**
 * @method InertiaTestResponse get($uri, array $headers = [])
 */
class TestCase extends TestbenchTestCase
{
    use RefreshDatabase, MakesGraphQLRequests, UsesTestSchema, InertiaAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->setUpTestSchema();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Utils/Database/Migrations');

        // Point public path to package dir, because Inertia/Laravel Mix
        $this->app->instance('path.public', 'public');
    }

    protected function getPackageProviders($app)
    {
        return [
            LighthouseServiceProvider::class, // Bootstrap "nuwave/lighthouse"
            TracingServiceProvider::class, // Enable tracing from "nuwave/lighthouse"    
            LighthouseDashboardServiceProvider::class, // this package
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setupConnections($app);
        $this->setupLighthouse($app);
    }

    /**
     * Settings from "nuwave/lighthouse"
     */
    private function setupLighthouse($app)
    {
        // Use testing namespaces
        $app['config']->set('lighthouse.namespaces', [
            'models' => [
                'Tests\\Utils\\Models'
            ]
        ]);

        // Disable cache because we change SDL a lot.
        $app['config']->set('lighthouse.cache.enable', false);
    }

    /**
     * Lighthouse Dashboard database connection
     */
    private function setupConnections($app)
    {
        // Dashboard database connection
        $app['config']->set('database.connections.dashboard', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('database.default', 'dashboard');
    }

    /**
     * Execute same request "N" times
     */
    protected function graphQLTimes(int $times, string $query)
    {
        for ($i = 1; $i <= $times; $i++) {
            $this->graphQL($query);
        }
    }

    /**
     * Allow to switch between schemas on same test method.
     */
    protected function rebuildTestSchema()
    {
        // TODO not working
        $this->app->extend(SchemaSourceProvider::class, fn () => new TestSchemaProvider($this->schema));
    }
}
