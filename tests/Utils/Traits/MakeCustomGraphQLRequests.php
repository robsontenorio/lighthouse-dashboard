<?php

namespace Tests\Utils\Traits;

use App\Models\Client;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

trait MakeCustomGraphQLRequests
{
    use MakesGraphQLRequests;

    // how many requests
    protected int $times = 1;

    // graphQL query
    protected string $query;

    // set specific duration for a operation
    protected int $duration;

    private static function new()
    {
        return new static;
    }

    /**
     * Parametrized graphQL request.
     */
    protected function customGraphQLRequest()
    {
        self::new();

        return $this;
    }

    /**
     * Request "N" times with same query.
     */
    protected function times(int $times)
    {
        $this->times = $times;

        return $this;
    }

    /**
     * Set when this request was made.
     * 
     *  @param string $string  Any valid string to be parsed by Carbon.
     */
    protected function withDateTime(string $string = null)
    {
        // Always travel back to "now" before travelling time.
        $this->travelBack();

        if ($string) {
            $this->travelTo(Carbon::parse($string));
        }

        return $this;
    }

    /**
     * Set specific duration for a operation.
     * 
     * @param int $nanoseconds 
     */
    protected function withDuration(int $nanoseconds)
    {
        $this->duration = $nanoseconds;

        return $this;
    }

    /**
     * Set request as specific Client.
     */
    protected function forClient(Client $client)
    {
        // Actually a Client will be parsed from authenticated User.
        $user = new User();
        $user->username = $client->username;

        $this->actingAs($user);

        return $this;
    }

    /**
     * Make a graphQL query request.
     * 
     * @param string $query  A graphQL query.
     */
    protected function query(string $query)
    {
        // remember latest request before make new requests
        $latestRequest = Request::isOperation()->latest('id')->first();

        for ($i = 1; $i <= $this->times; $i++) {
            $this->graphQL($query);
        }

        if (isset($this->duration)) {
            $this->updateDurationAfter($latestRequest);
        }

        // Always travel back to "now" after requests
        $this->travelBack();
    }

    /**
     * Update duration considering latest request mark point
     */
    protected function updateDurationAfter(?Request $latestRequest)
    {
        $operation = Request::isOperation()->latest('id')->first()->operation;

        $operation->requests()
            ->isOperation()
            ->when($latestRequest, function ($query) use ($latestRequest) {
                $query->where('id', '>', $latestRequest->id);
            })
            ->update([
                'duration' => $this->duration
            ]);
    }
}
