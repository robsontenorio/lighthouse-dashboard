<?php

namespace Tests\Utils\Traits;

use Tests\InertiaTestResponse;

trait InertiaAssertions
{
    protected function createTestResponse($response)
    {
        return InertiaTestResponse::fromBaseResponse($response);
    }
}
