<?php

namespace CommonTest\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\CachingQueryServiceFactory;
use Common\Service\Cqrs\Query\QueryService;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CachingQueryServiceFactoryTest
 * @package CommonTest\Service\Cqrs\Query
 */
class CachingQueryServiceFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $config = [
            'query_cache' => [
                'enabled' => true,
                'ttl' => [
                    'query type' => 300
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn(m::mock(\Zend\Log\LoggerInterface::class));
        $mockSl->shouldReceive('get')->with(QueryService::class)->andReturn(m::mock(QueryService::class));
        $mockSl->shouldReceive('get')->with(CacheEncryption::class)->andReturn(m::mock(CacheEncryption::class));

        $sut = new CachingQueryServiceFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(CachingQueryService::class, $service);
    }

    public function testCreateServiceMissingQueryCacheConfig()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Query cache config key missing');

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new CachingQueryServiceFactory();
        $sut->createService($mockSl);
    }

    public function testCreateServiceMissingQueryCacheEnabledConfig()
    {
        $config = [
            'query_cache' => [
                'ttl' => [
                    'query type' => 300
                ],
            ],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Query cache enabled/disabled config key missing');

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new CachingQueryServiceFactory();
        $sut->createService($mockSl);
    }
}
