<?php

declare(strict_types=1);

namespace Common\Test\Builder;

use ZfcRbac\Service\AuthorizationService;
use Mockery as m;
use Mockery\MockInterface;

class AuthorizationServiceMockBuilder
{
    /**
     * @return AuthorizationService|MockInterface
     */
    public function build(): MockInterface
    {
        $service = m::mock(AuthorizationService::class);
        $service->shouldReceive('isGranted')->andReturn(false)->byDefault();
        return $service;
    }
}
