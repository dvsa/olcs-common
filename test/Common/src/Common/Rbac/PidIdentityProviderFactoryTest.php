<?php

namespace CommonTest\Common\Rbac;

use Common\Rbac\PidIdentityProviderFactory;
use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * Class IdentityProviderFactoryTest
 * @package CommonTest\Rbac
 */
class PidIdentityProviderFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockQuerySender = m::mock(QuerySender::class);
        $mockRequest = m::mock(Request::class);
        $cache = m::mock(CacheEncryption::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('QuerySender')->andReturn($mockQuerySender);
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with(CacheEncryption::class)->andReturn($cache);

        $sut = new PidIdentityProviderFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(IdentityProviderInterface::class, $service);
    }
}
