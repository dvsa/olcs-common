<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\Application;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Data\Generic as GenericDataService;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ApplicationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationTest extends MockeryTestCase
{
    /**
     * Tests exception thrown if there is no application
     *
     * @group publicationFilter
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testNoApplicationException()
    {
        $input = new Publication();
        $sut = new Application();

        $mockAppService = m::mock(GenericDataService::class);
        $mockAppService->shouldReceive('fetchOne')->andReturn(false);

        $mockServiceManager = m::mock(ServiceLocatorInterface::class);
        $mockServiceManager->shouldReceive('get')->with('Generic\Service\Data\Application')->andReturn($mockAppService);

        $sut->setServiceLocator($mockServiceManager);

        $sut->filter($input);
    }

    /**
     * @dataProvider filterProvider
     *
     * @group publicationFilter
     *
     * @param array $operatingCentres
     */
    public function testFilter($operatingCentres, $expectedOperatingCentre)
    {
        $appId = 7;
        $transportManagers = [];

        $appData = [
            'id' => $appId,
            'operatingCentres' => $operatingCentres,
            'transportManagers' => $transportManagers
        ];

        $expectedOutput = [
            'applicationData' => $appData,
            'operatingCentreData' => $operatingCentres,
            'transportManagerData' => $transportManagers
        ];

        $input = new Publication();
        $sut = new Application();

        $mockAppService = m::mock(GenericDataService::class);
        $mockAppService->shouldReceive('fetchOne')->andReturn($appData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('Generic\Service\Data\Application')->andReturn($mockAppService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * Provider for testFilter
     *
     * @return array
     */
    public function filterProvider()
    {
        return [
            [[],[]],
            [
                [
                    0 => [
                        'operatingCentre' => [
                            'address' => 'address'
                        ]
                    ]
                ],
                'address'
            ]
        ];
    }
}
