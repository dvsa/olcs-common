<?php

namespace CommonTest\Rbac\Role;

use Common\Rbac\Role\RoleProviderFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Role\RoleProviderInterface;

/**
 * Class RoleProviderFactoryTest
 * @package CommonTest\Rbac
 */
class RoleProviderFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockQuerySender = m::mock(QuerySender::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('QuerySender')->andReturn($mockQuerySender);

        $sut = new RoleProviderFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(RoleProviderInterface::class, $service);
    }
}
