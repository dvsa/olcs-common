<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\QueryServiceInterface;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Cache\Storage\StorageInterface;

/**
 * @covers Common\Service\Cqrs\Query\CachingQueryService
 */
class CachingQueryServiceTest extends MockeryTestCase
{
    /** @var  CachingQueryService */
    private $sut;

    /** @var  QueryContainerInterface | m\MockInterface */
    private $mockQuery;
    /** @var  QueryServiceInterface | m\MockInterface */
    private $mockQS;
    /** @var  StorageInterface | m\MockInterface */
    private $mockCache;
    /** @var  m\MockInterface */
    private $mockResult;

    public function setUp()
    {
        $this->mockQuery = m::mock(QueryContainerInterface::class);
        $this->mockCache = m::mock(StorageInterface::class);

        $this->mockResult = m::mock(\Dvsa\Olcs\Api\Domain\Command\Result::class);

        $this->mockQS = m::mock(QueryServiceInterface::class);
        $this->mockQS
            ->shouldReceive('setRecoverHttpClientException')
            ->shouldReceive('send')
            ->with($this->mockQuery)
            ->once()
            ->andReturn($this->mockResult);

        $this->sut = new CachingQueryService($this->mockQS, $this->mockCache);
    }

    public function testSendWithNoCache()
    {
        $this->mockQuery
            ->shouldReceive('isMediumTermCachable')->once()->andReturn(false)
            ->shouldReceive('isShortTermCachable')->once()->andReturn(false);

        static::assertSame($this->mockResult, $this->sut->send($this->mockQuery));
    }

    public function testSendWithShortCacheNull()
    {
        $this->mockQuery
            ->shouldReceive('isMediumTermCachable')->once()->andReturn(false)
            ->shouldReceive('isShortTermCachable')->once()->andReturn(true)
            ->shouldReceive('getCacheIdentifier')->once()->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(false);

        static::assertSame($this->mockResult, $this->sut->send($this->mockQuery));
    }

    public function testSendWithShortCache()
    {
        $this->mockQuery
            ->shouldReceive('isMediumTermCachable')->times(2)->andReturn(false)
            ->shouldReceive('isShortTermCachable')->times(2)->andReturn(true)
            ->shouldReceive('getDto')->once()->andReturn(new \stdClass)
            ->shouldReceive('getCacheIdentifier')->times(2)->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(true);

        $this->sut->send($this->mockQuery);
        $this->sut->send($this->mockQuery);
    }

    public function testSendWithMediumCache()
    {
        $this->mockQuery
            ->shouldReceive('isMediumTermCachable')->times(2)->andReturn(true)
            ->shouldReceive('isShortTermCachable')->never()
            ->shouldReceive('getDto')->once()->andReturn(new \stdClass)
            ->shouldReceive('getCacheIdentifier')->times(2)->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(true);

        $this->mockCache
            ->shouldReceive('hasItem')->with('cache_key')->andReturnValues([false, true])
            ->shouldReceive('setItem')->with('cache_key', $this->mockResult)->once()
            ->shouldReceive('getItem')->with('cache_key')->once()->andReturn($this->mockResult);

        $this->sut->send($this->mockQuery);
        $this->sut->send($this->mockQuery);
    }

    public function testSendWithMediumCacheAndLog()
    {
        $this->mockQuery
            ->shouldReceive('isMediumTermCachable')->times(2)->andReturn(true)
            ->shouldReceive('isShortTermCachable')->never()
            ->shouldReceive('getDto')->once()->andReturn(new \stdClass)
            ->shouldReceive('getCacheIdentifier')->times(2)->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(true);

        $this->mockCache
            ->shouldReceive('hasItem')->with('cache_key')->andReturnValues([false, true])
            ->shouldReceive('setItem')->with('cache_key', $this->mockResult)->once()
            ->shouldReceive('getItem')->with('cache_key')->once()->andReturn($this->mockResult);

        /** @var \Zend\Log\LoggerInterface $mockLogger */
        $mockLogger = m::mock(\Zend\Log\LoggerInterface::class)
            ->shouldReceive('debug')->with('Get from presistent cache ' . \stdClass::class)->once()
            ->getMock();

        $this->sut->setLogger($mockLogger);

        $this->sut->send($this->mockQuery);
        $this->sut->send($this->mockQuery);
    }
}
