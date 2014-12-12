<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PreviousUnpublished;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PreviousUnpublishedTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousUnpublishedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the filter returns the correct data
     */
    public function testFilter()
    {
        $publication = 1;
        $publicationSection = 13;
        $pi = 1;
        $id = 1;
        $version = 1;

        $data = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'pi' => $pi,
        ];

        $expectedOutput = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'pi' => $pi,
            'id' => $id,
            'version' => $version
        ];

        $input = new Publication($data);
        $sut = new PreviousUnpublished();

        $params = [
            'publication' => $publication,
            'publicationSection' => $publicationSection,
            'pi' => $pi,
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
