<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\LastHearing;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class LastHearingTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LastHearingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests exception thrown if there is no hearing is found
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     * @group publicationFilter
     */
    public function testNoPublicationException()
    {
        $input = new Publication();
        $sut = new LastHearing();

        $mockPiHearingService = m::mock('Common\Service\Data\PiHearing');
        $mockPiHearingService->shouldReceive('fetchList')->andReturn(false);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\PiHearing')->andReturn($mockPiHearingService);

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
        $pubType = 'A&D';
        $trafficArea = 'B';
        $piVenue = 1;
        $pi = 2;

        $params = [
            'pi' => $pi,
            'sort' => 'id',
            'order' => 'DESC',
            'limit' => 1
        ];

        $data = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pi' => $pi
        ];

        $restData = [
            'Results' => [
                0 => [
                    'piVenue' => [
                        'id' => $piVenue
                    ]
                ]
            ]
        ];

        $expectedOutput = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pi' => $pi,
            'hearingData' => [
                'piVenue' => $piVenue
            ]
        ];

        $input = new Publication($data);
        $sut = new LastHearing();

        $mockPiHearingService = m::mock('Common\Service\Data\PiHearing');
        $mockPiHearingService->shouldReceive('fetchList')->with($params)->andReturn($restData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\PiHearing')->andReturn($mockPiHearingService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
