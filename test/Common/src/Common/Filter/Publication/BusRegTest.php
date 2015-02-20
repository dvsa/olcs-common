<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusReg;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegTest extends MockeryTestCase
{
    /**
     * Tests exception thrown if there is no bus reg data
     *
     * @group publicationFilter
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testNoBusRegException()
    {
        $busRegId = 15;

        $input = new Publication(['busReg' => $busRegId]);
        $sut = new BusReg();

        $mockBusRegService = m::mock('Generic\Service\Data\BusReg');
        $mockBusRegService->shouldReceive('fetchOne')->with($busRegId)->andReturn([]);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Generic\Service\Data\BusReg')->andReturn($mockBusRegService);

        $sut->setServiceLocator($mockServiceManager);

        $sut->filter($input);
    }

    /**
     * @group publicationFilter
     *
     * Test the bus reg filter
     */
    public function testFilter()
    {
        $busRegId = 15;

        $busRegData = [
            'id' => $busRegId
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busReg' => $busRegId
        ];

        $input = new Publication(['busReg' => $busRegId]);
        $sut = new BusReg();

        $mockBusRegService = m::mock('\Generic\Service\Data\BusReg');
        $mockBusRegService->shouldReceive('fetchOne')->with($busRegId)->andReturn($busRegData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Generic\Service\Data\BusReg')->andReturn($mockBusRegService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
