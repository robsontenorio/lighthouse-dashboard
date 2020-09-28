<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SlientTracingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema.graphql');
    }

    public function test_silent_tracing_configuration()
    {
        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ')->assertJsonStructure(['data' => [], 'extensions' => ['tracing']]);

        config(['lighthouse-dashboard.silent_tracing' => true]);

        $this->graphQL('
            {
                products{
                    id
                    name
                }
            }
        ')->assertJsonPath('extensions.tracing', null);
    }
}
