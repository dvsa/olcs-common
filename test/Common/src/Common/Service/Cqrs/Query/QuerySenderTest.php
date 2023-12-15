<?php

namespace CommonTest\Common\Service\Cqrs\Query;

use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Query Sender Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QuerySenderTest extends MockeryTestCase
{
    protected $sut;

    protected $mockQueryService;
    protected $mockAnnotationBuilder;

    public function setUp(): void
    {
        $this->sut = new QuerySender();

        $this->mockQueryService = m::mock();
        $this->mockAnnotationBuilder = m::mock();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('QueryService')->andReturn($this->mockQueryService);
        $sm->shouldReceive('get')->with('TransferAnnotationBuilder')->andReturn($this->mockAnnotationBuilder);

        $this->sut->createService($sm);
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
