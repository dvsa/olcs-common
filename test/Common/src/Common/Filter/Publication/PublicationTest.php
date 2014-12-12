<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\Publication as PublicationFilter;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class PublicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests exception thrown if there is no publication found
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testNoPublicationException()
    {
        $input = new Publication();
        $sut = new PublicationFilter();

        $mockPublicationService = m::mock('Common\Service\Data\Publication');
        $mockPublicationService->shouldReceive('fetchList')->andReturn(false);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Common\Service\Data\Publication')->andReturn($mockPublicationService);

        $sut->setServiceLocator($mockServiceManager);

        $sut->filter($input);
    }

    /**
     * Tests the filter
     */
    public function testFilter()
    {
        $pubType = 'A&D';
        $trafficArea = 'B';
        $pubStatus = 'pub_s_new';
        $id = 1;
        $publicationNo = 6829;

        $params = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'pubStatus' => $pubStatus
        ];

        $data = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
        ];

        $restData = [
            'Results' => [
                0 => [
                    'id' => $id,
                    'publicationNo' => $publicationNo
                ]
            ]
        ];

        $expectedOutput = [
            'pubType' => $pubType,
            'trafficArea' => $trafficArea,
            'publication' => $id,
            'publicationNo' => $publicationNo
        ];

        $input = new Publication($data);
        $sut = new PublicationFilter();

        $mockPublicationService = m::mock('Common\Service\Data\Publication');
        $mockPublicationService->shouldReceive('fetchList')->with($params)->andReturn($restData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')
            ->with('\Common\Service\Data\Publication')
            ->andReturn($mockPublicationService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($output->getArrayCopy(), $expectedOutput);
    }
}
