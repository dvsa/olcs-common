<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousPublication;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PreviousPublicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousPublicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the filter
     *
     * @group publicationFilter
     */
    public function testFilter()
    {
        $pubType = 'A&D';
        $trafficArea = 'B';
        $pi = 1;
        $publicationNo = 6831;

        $params = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pi' => $pi,
            'limit' => 1000
        ];

        $data = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pi' => $pi,
            'publicationNo' => $publicationNo,
            'hearingData' => []
        ];

        $restData = [
            'Results' => [
                0 => [
                    'publication' => [
                        'publicationNo' => 6828
                    ]
                ],
                1 => [
                    'publication' => [
                        'publicationNo' => 6830
                    ]
                ],
                2 => [
                    'publication' => [
                        'publicationNo' => 6829
                    ]
                ]
            ]
        ];

        $expectedOutput = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pi' => $pi,
            'publicationNo' => $publicationNo,
            'hearingData' => [
                'previousPublication' => 6830
            ]
        ];

        $input = new Publication($data);
        $sut = new PreviousPublication();

        $mockPublicationLinkService = m::mock('Common\Service\Data\PublicationLink');
        $mockPublicationLinkService->shouldReceive('fetchList')->with($params)->andReturn($restData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('Common\Service\Data\PublicationLink')->andReturn($mockPublicationLinkService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
