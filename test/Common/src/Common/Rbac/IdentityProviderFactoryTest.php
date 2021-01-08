<?php

namespace CommonTest\Rbac;

use Common\Rbac\IdentityProviderFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * Class IdentityProviderFactoryTest
 * @package CommonTest\Rbac
 */
class IdentityProviderFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockQuerySender = m::mock(QuerySender::class);
        $mockRequest = m::mock(Request::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('QuerySender')->andReturn($mockQuerySender);
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);

        $sut = new IdentityProviderFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(IdentityProviderInterface::class, $service);
    }
}
