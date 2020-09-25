<?php

namespace Tests\Feature;

use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Database\Factories\ProductFactory;
use Tests\Utils\Traits\OperationAssertions;

class OperationsSlowTest extends TestCase
{
    use OperationAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');

        ProductFactory::times(5)->create();
    }

    public function test_slow_operations()
    {
        $this->customGraphQLRequest()
            ->withDuration(1000000)
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
            ->withDuration(2000000)
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
            ->assertPropCount("slowlestOperations", 2)
            ->assertPropValue("slowlestOperations", function ($data) {
                $this->assertEquals($data[0]['field']['name'], 'categories');
                $this->assertEquals($data[0]['field']['name'], 'categories');
            });
    }

    public function test_duration()
    {
        $this->customGraphQLRequest()
            ->withDuration(1000000)
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
            ->withDuration(2000000)
            ->times(5)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $expectedAverage = (5 * 1000000 + 5 * 2000000) / 10;

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("slowlestOperations", 1)
            ->assertPropValue("slowlestOperations", function ($data) use ($expectedAverage) {
                $this->assertEquals($data[0]['field']['name'], 'products');
                $this->assertEquals($data[0]['average_duration'], $expectedAverage);
                $this->assertEquals($data[0]['latest_duration'], 2000000);
            });
    }

    public function test_duration_with_filter()
    {
        $this->customGraphQLRequest()
            ->withDateTime("-20 days")
            ->withDuration(2000000)
            ->times(2)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $this->customGraphQLRequest()
            ->withDateTime("yesterday")
            ->withDuration(1000000)
            ->times(7)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $start_date = Carbon::parse('-30 days')->toDateString();
        $end_date = Carbon::parse('-20 days')->toDateString();

        $this->get("/lighthouse-dashboard/operations?start_date=in custom range&range[]={$start_date}&range[]={$end_date}")
            ->assertPropCount("slowlestOperations", 1)
            ->assertPropValue("slowlestOperations", function ($data) {
                $this->assertEquals($data[0]['field']['name'], 'products');
                $this->assertEquals($data[0]['average_duration'], 2000000);
                $this->assertEquals($data[0]['latest_duration'], 2000000);
            });
    }
}
