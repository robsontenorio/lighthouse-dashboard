<?php

namespace App\Listeners;

use App\Actions\StoreMetrics;
use App\Models\Schema;
use Nuwave\Lighthouse\Events\ManipulateResult;

class ManipulateResultListener
{
    public function handle(ManipulateResult $result)
    {
        // Ignore introspection requests.
        if ($this->isIntrospectionRequest($result)) {
            return;
        }

        $schema = Schema::first();
        $request = request()->json()->all();
        $tracing = $this->getTracing($result);

        StoreMetrics::dispatchAfterResponse($schema, $request, $tracing);
    }

    private function getTracing($result)
    {
        return $result->result->extensions['tracing'];
    }

    private function isIntrospectionRequest($result)
    {
        return count($this->getTracing($result)['execution']['resolvers']) == 0;
    }
}
