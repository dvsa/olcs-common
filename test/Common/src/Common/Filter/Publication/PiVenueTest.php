<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PiVenue;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PiVenueTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiVenueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @group publicationFilter
     *
     * @param $piVenueId
     * @param $piVenueOther
     * @param $venueDetails
     * @param $expectedVenueInfo
     */
    public function testFilter($piVenueId, $piVenueOther, $venueDetails, $expectedVenueInfo)
    {
        $hearingData = [
            'piVenue' => $piVenueId,
            'piVenueOther' => $piVenueOther
        ];

        $input = new Publication(['hearingData' => $hearingData]);
        $sut = new PiVenue();

        $mockPiVenueService = m::mock('Common\Service\Data\PiVenue');
        $mockPiVenueService->shouldReceive('fetchById')->with($piVenueId)->andReturn($venueDetails);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')->with('Common\Service\Data\PiVenue')->andReturn($mockPiVenueService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $outputHearingData = $output->offsetGet('hearingData');

        $this->assertEquals($expectedVenueInfo, $outputHearingData['piVenueOther']);
    }

    /**
     * Provider for testFilter
     *
     * @return array
     */
    public function filterProvider()
    {
        return [
            [
                1,
                null,
                [
                    'name' => 'Venue name',
                    'address' => [
                        'addressLine1' => 'line 1',
                        'addressLine2' => 'line 2',
                        'addressLine3' => 'line 3',
                        'addressLine4' => 'line 4',
                        'town' => 'town',
                        'postcode' => 'postcode',
                    ]
                ],
                'Venue name, line 1, line 2, line 3, line 4, town, postcode'
            ],
            [
                1,
                null,
                [
                    'name' => 'Venue name',
                    'address' => [
                        'addressLine1' => 'line 1',
                        'addressLine4' => 'line 4',
                        'town' => 'town',
                        'postcode' => 'postcode',
                    ]
                ],
                'Venue name, line 1, line 4, town, postcode'
            ],
            [   //tests what happens when there is no venue id
                null,
                'Other Pi Venue info',
                null,
                'Other Pi Venue info'
            ],

        ];
    }
}
