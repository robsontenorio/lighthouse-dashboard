<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Database\Factories\ClientFactory;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Tests\Utils\Traits\OperationAssertions;

class OperationsWithDateFilterTest extends TestCase
{
    use OperationAssertions;

    public function setUp(): void
    {
        parent::setUp();

        $this->schema = File::get(__DIR__ . '/../Utils/Schemas/schema-full.graphql');
    }

    public function test_today()
    {
        // Must ignore these        
        $this->customGraphQLRequest()
            ->withDateTime("- 10 days")
            ->times(2)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must ignore these        
        $this->customGraphQLRequest()
            ->withDateTime("yesterday")
            ->times(5)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. It is "now".
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

        $this->get("/lighthouse-dashboard/operations?start_date=today")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 3);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }

    public function test_yesterday()
    {
        // Must ignore these     
        $this->customGraphQLRequest()
            ->withDateTime("-10 days")
            ->times(2)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. Because it is "yesterday".
        $this->customGraphQLRequest()
            ->withDateTime("yesterday")
            ->times(5)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. Because it is "today".
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

        $this->get("/lighthouse-dashboard/operations?start_date=yesterday")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 8);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }

    public function test_last_week()
    {
        // Must ignore these. 
        $this->customGraphQLRequest()
            ->withDateTime("-20 days")
            ->times(2)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. Because it is "last week" range.
        // The "last week" starts from  "monday of last week"
        $this->customGraphQLRequest()
            ->withDateTime("-10 days")
            ->times(4)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. Because it is "last week" range.
        $this->customGraphQLRequest()
            ->withDateTime("-7 days")
            ->times(3)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Must get these. Because it is "today".
        $this->customGraphQLRequest()
            ->times(6)
            ->query('
            {
                products{
                    id
                    name
                }
            }
        ');

        $this->get("/lighthouse-dashboard/operations?start_date=last week")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 13);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }

    public function test_default_start_date_is_past_month()
    {
        // Must ignore these.
        $this->customGraphQLRequest()
            ->withDateTime("-60 days")
            ->times(3)
            ->query('
                    {
                        products{
                            id
                            name
                        }
                    }
                ');

        // Must get these. Because in range of "last month"
        $this->customGraphQLRequest()
            ->withDateTime("-30 days")
            ->times(3)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        $this->get("/lighthouse-dashboard/operations")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 3);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }

    public function test_handle_inverted_custom_range_dates()
    {
        $this->customGraphQLRequest()
            ->withDateTime("-10 days")
            ->times(3)
            ->query('
                {
                    products{
                        id
                        name
                    }
                }
            ');

        // Simulating "start date" is great then "end date"
        $start = Carbon::parse("- 3 days")->toDateString();
        $end = Carbon::parse("- 20 days")->toDateString();

        // It is enough smart and fix dates, if inverted, to get metrics in right range
        $this->get("/lighthouse-dashboard/operations?start_date=in custom range&range[]={$start}&range[]={$end}")
            ->assertPropCount("topOperations", 1)
            ->assertPropValue("topOperations", function ($data) {
                $this->assertEquals($data[0]['total_requests'], 3);
                $this->assertEquals($data[0]['field']['name'], 'products');
            });
    }
}
