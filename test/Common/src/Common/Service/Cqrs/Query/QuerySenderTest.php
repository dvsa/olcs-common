<?php

namespace CommonTest\Common\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class QuerySenderTest extends MockeryTestCase
{
    protected $sut;

    protected $mockQueryService;
    protected $mockAnnotationBuilder;

    public function setUp(): void
    {
        $this->sut = new QuerySender();

        $this->mockQueryService = m::mock(CachingQueryService::class);
        $this->mockAnnotationBuilder = m::mock();

        $sm = m::mock(ContainerInterface::class);
        $sm->shouldReceive('get')->with('QueryService')->andReturn($this->mockQueryService);
        $sm->shouldReceive('get')->with('TransferAnnotationBuilder')->andReturn($this->mockAnnotationBuilder);

        $this->sut->__invoke($sm, QuerySender::class);
    }

    /**
     * @param QueryInterface $query
     * @return \Common\Service\Cqrs\Response
     */
    public function testSend()
    {
        $query = m::mock(QueryInterface::class);
        $constructedQuery = m::mock();

        $this->mockAnnotationBuilder->shouldReceive('createQuery')
            ->once()
            ->with($query)
            ->andReturn($constructedQuery);

        $this->mockQueryService
            ->shouldReceive('setRecoverHttpClientException')
            ->once()
            ->shouldReceive('send')
            ->once()
            ->with($constructedQuery)
            ->andReturn('RESULT');

        $this->assertEquals('RESULT', $this->sut->send($query));
    }
}
