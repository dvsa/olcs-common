<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousApplicationPublication;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PreviousApplicationPublicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousApplicationPublicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the filter
     *
     * @group publicationFilter
     * @dataProvider filterProvider
     */
    public function testFilter($additionalParam, $status)
    {
        $pubType = 'A&D';
        $trafficArea = 'B';
        $publicationNo = 6831;
        $id = 7;

        $params = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'limit' => 'all',
            $additionalParam => $id
        ];

        $data = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'publicationNo' => $publicationNo,
            'application' => $id,
            'licence' => $id,
            'applicationData' => [
                'status' => [
                    'id' => $status
                ]
            ]
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
            'publicationNo' => $publicationNo,
            'application' => $id,
            'licence' => $id,
            'applicationData' => [
                'status' => [
                    'id' => $status
                ]
            ],
            'previousPublication' => 6830
        ];

        $input = new Publication($data);
        $sut = new PreviousApplicationPublication();

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

    public function filterProvider()
    {
        $sut = new PreviousApplicationPublication();

        return [
            ['licence', $sut::APP_GRANTED_STATUS],
            ['licence', $sut::APP_REFUSED_STATUS],
            ['licence', $sut::APP_NTU_STATUS],
            ['licence', $sut::APP_CURTAILED_STATUS],
            ['application', 'some_status']
        ];
    }
}
