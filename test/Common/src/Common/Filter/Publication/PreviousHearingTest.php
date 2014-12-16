<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousHearing;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PreviousHearingTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousHearingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the filter
     *
     * @group publicationFilter
     */
    public function testFilter()
    {
        $pi = 1;

        $params = [
            'pi' => $pi,
            'limit' => 1000
        ];

        $data = [
            'pi' => $pi,
            'hearingData' => [
                'hearingDate' => [
                    '2014-03-16 14:30:00'
                ]
            ]
        ];

        $restData = [
            'Results' => [
                0 => [
                    'hearingDate' => '2014-02-21T10:15:00+0000',
                    'isAdjourned' => 'Y'
                ],
                1 => [
                    'hearingDate' => '2014-01-30T15:45:00+0000',
                    'isAdjourned' => 'N'
                ],
            ]
        ];

        $expectedOutput = [
            'pi' => $pi,
            'hearingData' => [
                'hearingDate' => [
                    '2014-03-16 14:30:00'
                ],
                'previousHearing' => [
                    'isAdjourned' => true,
                    'date' => '21 February 2014'
                ]
            ]
        ];

        $input = new Publication($data);
        $sut = new PreviousHearing();

        $mockPiHearingService = m::mock('Common\Service\Data\PiHearing');
        $mockPiHearingService->shouldReceive('fetchList')->with($params)->andReturn($restData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\PiHearing')
            ->andReturn($mockPiHearingService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
