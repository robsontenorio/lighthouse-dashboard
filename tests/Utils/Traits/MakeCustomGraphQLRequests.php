<?php

namespace Tests\Utils\Traits;

use App\Models\Client;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

trait MakeCustomGraphQLRequests
{
    use MakesGraphQLRequests;

    protected int $times = 1;
    protected string $query;
    protected string $travel;

    private static function new()
    {
        return new static;
    }

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
     * Any valid string to be parsed by Carbon.
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
     * Make request as specific Client.
     */
    protected function forClient(Client $client)
    {
        // In practice a Client will be parsed from authenticated User.
        $user = new User();
        $user->username = $client->username;

        $this->actingAs($user);

        return $this;
    }

    /**
     * Make a graphQL query request
     */
    protected function query(string $query)
    {
        for ($i = 1; $i <= $this->times; $i++) {
            $this->graphQL($query);
        }

        // Always travel back to "now" after requests
        $this->travelBack();
    }
}
