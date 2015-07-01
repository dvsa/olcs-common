<?php

/**
 * Class PreviousUnpublishedApplicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousUnpublishedApplication;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class PreviousUnpublishedApplicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousUnpublishedApplicationTest extends MockeryTestCase
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
        $application = 8;
        $id = 1;
        $version = 1;

        $data = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'application' => $application,
        ];

        $expectedOutput = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'application' => $application,
            'id' => $id,
            'version' => $version
        ];

        $input = new Publication($data);
        $sut = new PreviousUnpublishedApplication();

        $params = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'application' => $application,
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
