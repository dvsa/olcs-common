<?php

namespace CommonTest\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\User\LastLoginService;
use Common\Service\User\LastLoginServiceFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LastLoginServiceFactoryTest
 * @package CommonTest\Service
 */
class LastLoginServiceFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockCommandSender = m::mock(CommandSender::class);

        $mockServiceLocator = m::mock(ServiceLocatorInterface::class);
        $mockServiceLocator->shouldReceive('get')->with('CommandSender')->andReturn($mockCommandSender);

        $sut = new LastLoginServiceFactory();
        $instance = $sut->createService($mockServiceLocator);

        $this->assertInstanceOf(LastLoginService::class, $instance);
    }
}
