<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\CurrentUser;
use Common\View\Helper\CurrentUserFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class CurrentUserFactoryTest
 * @package CommonTest\View\Helper
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

        $this->assertInstanceOf(CurrentUser::class, $service);
    }
}
