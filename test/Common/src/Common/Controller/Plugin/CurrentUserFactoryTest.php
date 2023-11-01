<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\CurrentUserFactory;
use Common\Controller\Plugin\CurrentUserInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Class CurrentUserFactoryTest
 * @package CommonTest\Controller\Plugin
 */
class CurrentUserFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockAuth = m::mock(AuthorizationService::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuth);
        $sut = new CurrentUserFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(CurrentUserInterface::class, $service);
    }
}
