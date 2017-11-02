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

    public function mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse, $calls = 1)
    {
        $this->mockServiceLocator = m::mock('\Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('TransferAnnotationBuilder')
            ->andReturn($mockTransferAnnotationBuilder)
            ->times($calls)
            ->shouldReceive('get')
            ->with('QueryService')
            ->andReturn(
                m::mock()
                    ->shouldReceive('send')
                    ->with('query')
                    ->andReturn($mockResponse)
                    ->times($calls)
                    ->getMock()
            )
            ->times($calls)
            ->getMock();

        $sut->setServiceLocator($this->mockServiceLocator);
    }
}
