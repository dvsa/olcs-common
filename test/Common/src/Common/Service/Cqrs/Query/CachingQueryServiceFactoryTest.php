<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\CachingQueryServiceFactory;
use Common\Service\Cqrs\Query\QueryService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\Adapter\Redis;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CachingQueryServiceFactoryTest
 * @package CommonTest\Service\Cqrs\Query
 */
class CachingQueryServiceFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn(m::mock(\Zend\Log\LoggerInterface::class));
        $mockSl->shouldReceive('get')->with(QueryService::class)->andReturn(m::mock(QueryService::class));
        $mockSl->shouldReceive('get')->with(Redis::class)->andReturn(m::mock(StorageInterface::class));

        $sut = new CachingQueryServiceFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(CachingQueryService::class, $service);
    }
}
