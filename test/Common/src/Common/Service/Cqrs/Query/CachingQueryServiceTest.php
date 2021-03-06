<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\QueryServiceInterface;
use Dvsa\Olcs\Transfer\Query\Cache\ById;
use Dvsa\Olcs\Transfer\Query\CacheableLongTermQueryInterface;
use Dvsa\Olcs\Transfer\Query\CacheableMediumTermQueryInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Http\Response;

/**
 * @covers Common\Service\Cqrs\Query\CachingQueryService
 */
class CachingQueryServiceTest extends MockeryTestCase
{
    /** @var QueryContainerInterface | m\MockInterface */
    private $mockQuery;

    /** @var QueryServiceInterface | m\MockInterface */
    private $mockQS;

    /** @var CacheEncryptionService | m\MockInterface */
    private $mockCache;

    /** @var AnnotationBuilder | m\MockInterface */
    private $mockAnnotationBuilder;

    /** @var m\MockInterface */
    private $mockResult;

    public function setUp(): void
    {
        $this->mockQuery = m::mock(QueryContainerInterface::class);
        $this->mockCache = m::mock(CacheEncryptionService::class);
        $this->mockAnnotationBuilder = m::mock(AnnotationBuilder::class);

        $this->mockResult = m::mock(\Dvsa\Olcs\Api\Domain\Command\Result::class);

        $this->mockQS = m::mock(QueryServiceInterface::class);
        $this->mockQS
            ->shouldReceive('setRecoverHttpClientException')
            ->shouldReceive('send')
            ->with($this->mockQuery)
            ->andReturn($this->mockResult);
    }

    public function testHandleCacheDisabled()
    {
        $sut = new CachingQueryService($this->mockQS, $this->mockCache, $this->mockAnnotationBuilder, false, $this->ttlValues());

        static::assertSame($this->mockResult, $sut->send($this->mockQuery));
    }

    public function testHandleCustomCache()
    {
        $identifier = 'identifier';
        $uniqueId = 'unique id';
        $cacheResult = 'result';

        $this->mockCache->expects('hasCustomItem')->with($identifier, $uniqueId)->andReturnTrue();
        $this->mockCache->expects('getCustomItem')->with($identifier, $uniqueId)->andReturn($cacheResult);

        $sut = new CachingQueryService($this->mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        self::assertEquals($cacheResult, $sut->handleCustomCache($identifier, $uniqueId));
    }

    public function testCustomCacheMissingThenLoadFromDb()
    {
        $identifier = 'identifier';
        $uniqueId = 'unique id';
        $cacheResult = 'result';

        $cqrsQueryContainer = m::mock(QueryContainerInterface::class);
        $cqrsQueryContainer->expects('isPersistentCacheable')->andReturnFalse();
        $cqrsQueryContainer->expects('isShortTermCacheable')->andReturnFalse();

        $this->mockCache->expects('hasCustomItem')->with($identifier, $uniqueId)->andReturnFalse();

        $this->mockAnnotationBuilder->expects('createQuery')
            ->with(m::type(ById::class))
            ->andReturn($cqrsQueryContainer);

        $response = m::mock(Response::class);
        $response->expects('isOk')->withNoArgs()->andReturnTrue();
        $response->expects('getResult')->withNoArgs()->andReturn($cacheResult);

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException');
        $mockQS->expects('send')->with($cqrsQueryContainer)->andReturn($response);

        $sut = new CachingQueryService($mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        self::assertEquals($cacheResult, $sut->handleCustomCache($identifier, $uniqueId));
    }

    public function testCustomCacheExceptionThenDbFail()
    {
        $httpFailureCode = 418;
        $expectedMsg = sprintf(CachingQueryService::BACKEND_FAIL_MSG, $httpFailureCode);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedMsg);

        $identifier = 'identifier';
        $uniqueId = 'unique id';
        $cacheResult = 'result';

        $cqrsQueryContainer = m::mock(QueryContainerInterface::class);
        $cqrsQueryContainer->expects('isPersistentCacheable')->andReturnFalse();
        $cqrsQueryContainer->expects('isShortTermCacheable')->andReturnFalse();

        $this->mockCache->expects('hasCustomItem')->with($identifier, $uniqueId)->andReturnTrue();
        $this->mockCache->expects('getCustomItem')->with($identifier, $uniqueId)->andThrow(new \Exception());

        $this->mockAnnotationBuilder->expects('createQuery')
            ->with(m::type(ById::class))
            ->andReturn($cqrsQueryContainer);

        $response = m::mock(Response::class);
        $response->expects('isOk')->withNoArgs()->andReturnFalse();
        $response->expects('getStatusCode')->withNoArgs()->andReturn($httpFailureCode);

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException');
        $mockQS->expects('send')->with($cqrsQueryContainer)->andReturn($response);

        $sut = new CachingQueryService($mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        self::assertEquals($cacheResult, $sut->handleCustomCache($identifier, $uniqueId));
    }

    public function testSendWithNoCache()
    {
        $this->mockQuery
            ->shouldReceive('isPersistentCacheable')->once()->andReturnFalse()
            ->shouldReceive('isShortTermCacheable')->once()->andReturnFalse();

        $sut = new CachingQueryService($this->mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        static::assertSame($this->mockResult, $sut->send($this->mockQuery));
    }

    public function testSendWithShortCacheNull()
    {
        $this->mockQuery
            ->shouldReceive('isPersistentCacheable')->once()->andReturnFalse()
            ->shouldReceive('isShortTermCacheable')->once()->andReturnTrue()
            ->shouldReceive('getDtoClassName')->once()->andReturn('dto_class_name')
            ->shouldReceive('getCacheIdentifier')->once()->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(false);

        $sut = new CachingQueryService($this->mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        static::assertSame($this->mockResult, $sut->send($this->mockQuery));
    }

    public function testSendWithShortCache()
    {
        $this->mockQuery
            ->shouldReceive('isPersistentCacheable')->times(2)->andReturnFalse()
            ->shouldReceive('isShortTermCacheable')->times(2)->andReturnTrue()
            ->shouldReceive('getDtoClassName')->twice()->andReturn('dto_class_name')
            ->shouldReceive('getCacheIdentifier')->times(2)->andReturn('cache_key');

        $this->mockResult->shouldReceive('isOk')->with()->once()->andReturn(true);

        $sut = new CachingQueryService($this->mockQS, $this->mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        $sut->send($this->mockQuery);
        $sut->send($this->mockQuery);
    }

    /**
     * Test exception is thrown when the query doesn't have any of the possible query interfaces
     */
    public function testPersistentCacheMissingQueryInterface()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->expects('isPersistentCacheable')->andReturnTrue();
        $mockQuery->expects('isMediumTermCacheable')->andReturnFalse();
        $mockQuery->expects('isLongTermCacheable')->andReturnFalse();
        $mockQuery->expects('getDtoClassName')->andReturn('dto_class_name');
        $mockQuery->expects('getCacheIdentifier')->andReturn('cache_key');
        $mockQuery->expects('getEncryptionMode')->andReturn('encryption_mode');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException');
        $mockQS->expects('send')->with($mockQuery)->andReturn($this->mockResult);

        $this->mockResult->expects('isOk')->andReturnTrue();

        $mockCache = m::mock(CacheEncryptionService::class);
        $mockCache->expects('hasItem')->with('cache_key', 'encryption_mode')->andReturnFalse();

        $mockLogger = m::mock(\Laminas\Log\LoggerInterface::class);
        $mockLogger->expects('debug')->with('Using encryption mode: encryption_mode')->ordered();
        $mockLogger->expects('debug')->with('Storing in local cache: dto_class_name')->ordered();
        $mockLogger->expects('err')->with('Cache failure: No TTL value found for this query')->ordered();

        $sut = new CachingQueryService($mockQS, $mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        $sut->setLogger($mockLogger);

        self::assertSame($this->mockResult, $sut->send($mockQuery));
    }

    /**
     * When the persistent cache is not populated
     *
     * Query is sent to the backend
     * Persistent and local caches both populated with the result
     *
     * @dataProvider dpPersistentCacheNotPopulated
     */
    public function testPersistentCacheNotPopulated($isMediumTerm, $cacheTtl)
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->expects('isPersistentCacheable')->andReturnTrue();
        $mockQuery->expects('isMediumTermCacheable')->andReturn($isMediumTerm);
        $mockQuery->expects('isLongTermCacheable')->times($isMediumTerm ? 0 : 1)->andReturnTrue();
        $mockQuery->expects('isShortTermCacheable')->never();
        $mockQuery->expects('getDtoClassName')->andReturn('dto_class_name');
        $mockQuery->expects('getCacheIdentifier')->andReturn('cache_key');
        $mockQuery->expects('getEncryptionMode')->andReturn('encryption_mode');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException');
        $mockQS->expects('send')->with($mockQuery)->andReturn($this->mockResult);

        $this->mockResult->expects('isOk')->andReturnTrue();

        $mockCache = m::mock(CacheEncryptionService::class);
        $mockCache->expects('hasItem')->with('cache_key', 'encryption_mode')->andReturnFalse();
        $mockCache->expects('setItem')->with('cache_key', 'encryption_mode', $this->mockResult, $cacheTtl)->andReturn();

        $mockLogger = m::mock(\Laminas\Log\LoggerInterface::class);
        $mockLogger->expects('debug')->with('Using encryption mode: encryption_mode')->ordered();
        $mockLogger->expects('debug')->with('Storing in local cache: dto_class_name')->ordered();
        $mockLogger->expects('debug')->with('Storing in persistent cache with TTL of ' . $cacheTtl . ' seconds: dto_class_name')->ordered();

        $sut = new CachingQueryService($mockQS, $mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        $sut->setLogger($mockLogger);

        self::assertSame($this->mockResult, $sut->send($mockQuery));
    }

    public function dpPersistentCacheNotPopulated()
    {
        return [
            [true, 300],
            [false, 43200],
        ];
    }

    /**
     * When a value is retrieved from the persistent cache, it is saved to the short term cache
     *
     * First checks the local cache (initially not present)
     * Second checks and retrieves from the persistent cache
     * Third checks retrieval from the short term cache
     */
    public function testRetrieveFromPersistentThenRetrieveFromLocal()
    {
        /**
         * Each test is called twice, except encryption as 2nd time we use local cache
         */
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->expects('isPersistentCacheable')->times(2)->andReturnTrue();
        $mockQuery->expects('isShortTermCacheable')->never();
        $mockQuery->expects('getDtoClassName')->twice()->andReturn('dto_class_name');
        $mockQuery->expects('getCacheIdentifier')->twice()->andReturn('cache_key');
        $mockQuery->expects('getEncryptionMode')->andReturn('encryption_mode');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException')->twice();

        /**
         * The query is sent twice, but we only go once to the persistent cache
         */
        $mockCache = m::mock(CacheEncryptionService::class);
        $mockCache->expects('hasItem')->with('cache_key', 'encryption_mode')->andReturnTrue();
        $mockCache->expects('getItem')->with('cache_key', true)->andReturn($this->mockResult);

        $mockLogger = m::mock(\Laminas\Log\LoggerInterface::class);
        $mockLogger->expects('debug')->with('Using encryption mode: encryption_mode')->ordered();
        $mockLogger->expects('debug')->with('Fetching from persistent cache: dto_class_name')->ordered();
        $mockLogger->expects('debug')->with('Storing in local cache: dto_class_name')->ordered();
        $mockLogger->expects('debug')->with('Fetching from local cache: dto_class_name')->ordered();

        $sut = new CachingQueryService($mockQS, $mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        $sut->setLogger($mockLogger);

        /**
         * first goes to persistent, second to local (backed up by logging order above)
         */
        self::assertSame($this->mockResult, $sut->send($mockQuery));
        self::assertSame($this->mockResult, $sut->send($mockQuery));
    }

    /**
     * Check that if there's an exception from the cache, the query is still executed
     */
    public function testRetrieveFromPersistentWithException()
    {
        $mockQuery = m::mock(QueryContainerInterface::class);
        $mockQuery->expects('isPersistentCacheable')->andReturnTrue();
        $mockQuery->expects('isShortTermCacheable')->andReturnFalse();
        $mockQuery->expects('getDtoClassName')->andReturn('dto_class_name');
        $mockQuery->expects('getCacheIdentifier')->andReturn('cache_key');
        $mockQuery->expects('getEncryptionMode')->andReturn('encryption_mode');

        $mockQS = m::mock(QueryServiceInterface::class);
        $mockQS->expects('setRecoverHttpClientException');
        $mockQS->expects('send')->with($mockQuery)->andReturn($this->mockResult);

        $mockCache = m::mock(CacheEncryptionService::class);
        $mockCache->expects('hasItem')->with('cache_key', 'encryption_mode')->andReturnTrue();
        $mockCache->expects('getItem')->with('cache_key', true)->andThrow(new \Exception('exception_msg'));

        $mockLogger = m::mock(\Laminas\Log\LoggerInterface::class);
        $mockLogger->expects('debug')->with('Using encryption mode: encryption_mode')->ordered();
        $mockLogger->expects('debug')->with('Fetching from persistent cache: dto_class_name')->ordered();
        $mockLogger->expects('err')->with('Cache failure: exception_msg')->ordered();

        $sut = new CachingQueryService($mockQS, $mockCache, $this->mockAnnotationBuilder, true, $this->ttlValues());
        $sut->setLogger($mockLogger);

        self::assertSame($this->mockResult, $sut->send($mockQuery));
    }

    public function ttlValues(): array
    {
        return [
            CacheableMediumTermQueryInterface::class => 300,
            CacheableLongTermQueryInterface::class => 43200,
        ];
    }
}
