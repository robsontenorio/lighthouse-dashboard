<?php

namespace App\Traits;

use App\Listeners\ManipulateResultListener;
use Nuwave\Lighthouse\Events\ManipulateResult;

trait DisableDashboardMetrics
{
    protected function withoutDashboardMetrics(): void
    {
        $this->swap(ManipulateResultListener::class, new class
        {
            public function handle(ManipulateResult $result)
            {
                unset($result->result->extensions['tracing']);
            }
        });
    }
}
