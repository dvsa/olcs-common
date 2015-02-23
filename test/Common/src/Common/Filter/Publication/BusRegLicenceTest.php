<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegLicence;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class BusRegLicenceTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegLicenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests exception thrown if there is no licence
     *
     * @group publicationFilter
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testNoLicenceException()
    {
        $input = new Publication();
        $sut = new BusRegLicence();

        $mockLicenceService = m::mock('Common\Service\Data\Licence');
        $mockLicenceService->shouldReceive('fetchLicenceData')->andReturn(false);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $sut->setServiceLocator($mockServiceManager);

        $sut->filter($input);
    }

    /**
     * Tests the filter
     *
     * @group publicationFilter
     */
    public function testFilter()
    {
        $licenceId = 7;

        $licenceData = [
            'id' => $licenceId,
        ];

        $expectedOutput = [
            'licence' => $licenceId,
            'licenceData' => $licenceData
        ];

        $input = new Publication();
        $sut = new BusRegLicence();

        $mockLicenceService = m::mock('Common\Service\Data\Licence');
        $mockLicenceService->shouldReceive('fetchLicenceData')->andReturn($licenceData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
