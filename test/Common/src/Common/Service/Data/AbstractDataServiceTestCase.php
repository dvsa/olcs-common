<?php

/**
 * Abstract Data Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Data;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Data Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractDataServiceTestCase extends MockeryTestCase
{
    protected $mockServiceLocator;

    /** @var m\Mock */
    private $mockQuerySender;

    public function mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse)
    {
        $this->setupQuerySender($sut, $mockTransferAnnotationBuilder);

        $this->mockQuerySender->shouldReceive('send')
            ->with('query')
            ->once()
            ->andReturn($mockResponse);
    }

    public function mockHandleSingleQuery($mockResponse, $queryContainer)
    {
        $this->mockQuerySender->shouldReceive('send')
            ->with($queryContainer)
            ->once()
            ->andReturn($mockResponse);
    }

    /**
     * @param $sut
     * @param $mockTransferAnnotationBuilder
     */
    public function setupQuerySender($sut, $mockTransferAnnotationBuilder)
    {
        $this->mockQuerySender = m::mock();
        $this->mockServiceLocator = m::mock('\Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('TransferAnnotationBuilder')
            ->andReturn($mockTransferAnnotationBuilder)
            ->shouldReceive('get')
            ->with('QueryService')
            ->andReturn($this->mockQuerySender)
            ->getMock();
        $sut->setServiceLocator($this->mockServiceLocator);
    }
}
