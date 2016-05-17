<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\QueryServiceInterface;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class CachingQueryServiceTest
 * @package CommonTest\Service\Cqrs\Query
 */
class CachingQueryServiceTest extends MockeryTestCase
{
    public function testSendWithNoCache()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->shouldReceive('isMediumTermCachable')->andReturn(false);
        $mockQuery->shouldReceive('isShortTermCachable')->andReturn(false);

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->shouldReceive('send')->with($mockQuery)->andReturn('value');

        $mockCache = m::mock(StorageInterface::class);

        $sut = new CachingQueryService($mockQS, $mockCache);

        $sut->send($mockQuery);
    }

    public function testSendWithLocalCache()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->shouldReceive('isMediumTermCachable')->andReturn(false);
        $mockQuery->shouldReceive('isShortTermCachable')->andReturn(true);
        $mockQuery->shouldReceive('getDto')->andReturn(new \StdClass);
        $mockQuery->shouldReceive('getCacheIdentifier')->andReturn('cache_key');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->shouldReceive('send')->with($mockQuery)->once()->andReturn('value');

        $mockCache = m::mock(StorageInterface::class);

        $sut = new CachingQueryService($mockQS, $mockCache);

        $sut->send($mockQuery);
        $sut->send($mockQuery);
    }

    public function testSendWithCache()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->shouldReceive('isMediumTermCachable')->andReturn(true);
        $mockQuery->shouldReceive('isShortTermCachable')->andReturn(true);
        $mockQuery->shouldReceive('getDto')->andReturn(new \stdClass);
        $mockQuery->shouldReceive('getCacheIdentifier')->andReturn('cache_key');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->shouldReceive('send')->with($mockQuery)->once()->andReturn('value');

        $mockCache = m::mock(StorageInterface::class);
        $mockCache->shouldReceive('hasItem')->with('cache_key')->andReturnValues([false, true]);
        $mockCache->shouldReceive('setItem')->with('cache_key', 'value')->once();
        $mockCache->shouldReceive('getItem')->with('cache_key')->once()->andReturn('value');

        $sut = new CachingQueryService($mockQS, $mockCache);

        $sut->send($mockQuery);
        $sut->send($mockQuery);
    }

    public function testSendWithCacheAndLog()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->shouldReceive('isMediumTermCachable')->andReturn(true);
        $mockQuery->shouldReceive('isShortTermCachable')->andReturn(true);
        $mockQuery->shouldReceive('getDto')->andReturn(new \stdClass);
        $mockQuery->shouldReceive('getCacheIdentifier')->andReturn('cache_key');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->shouldReceive('send')->with($mockQuery)->once()->andReturn('value');

        $mockCache = m::mock(StorageInterface::class);
        $mockCache->shouldReceive('hasItem')->with('cache_key')->andReturnValues([false, true]);
        $mockCache->shouldReceive('setItem')->with('cache_key', 'value')->once();
        $mockCache->shouldReceive('getItem')->with('cache_key')->once()->andReturn('value');

        $mockLogger = m::mock(\Zend\Log\LoggerInterface::class);
        $mockLogger->shouldReceive('debug')->with('Get from presistent cache '. \stdClass::class)->once();

        $sut = new CachingQueryService($mockQS, $mockCache);
        $sut->setLogger($mockLogger);

        $sut->send($mockQuery);
        $sut->send($mockQuery);
    }
}
