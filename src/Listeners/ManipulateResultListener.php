<?php

namespace App\Listeners;

use App\Actions\StoreMetrics;
use App\Models\Client;
use App\Models\Schema;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Events\ManipulateResult;

class ManipulateResultListener
{
    public function handle(ManipulateResult $result)
    {
        // Ignore introspection requests.
        if ($this->isIntrospectionRequest()) {
            return;
        }

        try {
            $client = $this->getClient();
            $schema = Schema::first();
            $payload = request()->json('query');
        } catch (\Throwable $th) {
            report($th);
            return;
        }

        // TODO
        if (config('app.env') === 'testing') {
            StoreMetrics::dispatchNow($client, $schema, $payload, $result);
        } else {
            StoreMetrics::dispatchAfterResponse($client, $schema, $payload, $result);
        }

        if (config('lighthouse-dashboard.silent_tracing')) {
            $this->muteTracingResponse($result);
        }
    }

    private function isIntrospectionRequest()
    {
        $requestContent = request()->getContent();

        // TODO 
        return strstr($requestContent, '__schema');
    }

    private function getClient()
    {
        $user = Auth::user();

        // If not authenticated return anonymous user
        if (!$user) {
            return Client::first();
        }

        $identifer = config('lighthouse-dashboard.client_identifier');

        return Client::firstOrCreate(['username' => $user->$identifer]);
    }

    private function muteTracingResponse($result)
    {
        unset($result->result->extensions['tracing']);
    }
}
