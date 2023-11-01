<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\CurrentUser;
use Common\View\Helper\CurrentUserFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Class CurrentUserFactoryTest
 * @package CommonTest\View\Helper
 */
class CurrentUserFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockAuth = m::mock(AuthorizationService::class);
        $config = [
            'auth' => [
                'user_unique_id_salt' => 'salt',
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuth);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new CurrentUserFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(CurrentUser::class, $service);
    }

    public function testMissingConfig()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(CurrentUserFactory::MSG_MISSING_ANALYTICS_CONFIG);

        $config = [
            'auth' => [],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $sut = new CurrentUserFactory();
        $sut->createService($mockSl);
    }
}
