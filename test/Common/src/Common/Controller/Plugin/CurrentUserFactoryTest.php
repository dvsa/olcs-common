<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\CurrentUserFactory;
use Common\Controller\Plugin\CurrentUserInterface;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use LmcRbacMvc\Service\AuthorizationService;

class CurrentUserFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $mockAuth = m::mock(AuthorizationService::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuth);
        $sut = new CurrentUserFactory();
        $service = $sut->__invoke($mockSl, CurrentUserInterface::class);

        $this->assertInstanceOf(CurrentUserInterface::class, $service);
    }
}
