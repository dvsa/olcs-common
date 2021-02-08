<?php

namespace Common\Test\Builder;

use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

class AuthorizationServiceMockBuilder implements BuilderInterface
{
    /**
     * @inheritDoc
     */
    public function build()
    {
        $service = m::mock(AuthorizationService::class);
        $service->shouldReceive('isGranted')->andReturn(false)->byDefault();
        return $service;
    }
}
