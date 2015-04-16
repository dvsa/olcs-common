<?php

/**
 * Class PreviousUnpublishedBusTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousUnpublishedBus;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class PreviousUnpublishedBusTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousUnpublishedBusTest extends MockeryTestCase
{
    /**
     * Tests the filter returns the correct data
     *
     * @group publicationFilter
     */
    public function testFilter()
    {
        $publication = 1;
        $publicationSection = 23;
        $busReg = 1;
        $id = 1;
        $version = 1;

        $data = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'busReg' => $busReg,
        ];

        $expectedOutput = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'busReg' => $busReg,
            'id' => $id,
            'version' => $version
        ];

        $input = new Publication($data);
        $sut = new PreviousUnpublishedBus();

        $params = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'busReg' => $busReg,
            'sort' => 'id',
            'order' => 'DESC'
        ];

        $restData = [
            'Results' => [
                0 => [
                    'id' => $id,
                    'version' => $version,
                    'publication' => [
                        'pubStatus' => [
                            'id' => 'pub_s_new'
                        ]
                    ]
                ]
            ]
        ];

        $mockPublicationLinkService = m::mock('Common\Service\Data\PublicationLink');
        $mockPublicationLinkService->shouldReceive('fetchList')->with($params)->andReturn($restData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\PublicationLink')
            ->andReturn($mockPublicationLinkService);

        $sut->setServiceLocator($mockServiceManager);

        $this->assertEquals($expectedOutput, $sut->filter($input)->getArrayCopy());
    }
}
