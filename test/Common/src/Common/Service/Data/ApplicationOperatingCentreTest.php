<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\ApplicationOperatingCentre;
use Mockery as m;

/**
 * Class ApplicationOperatingCentre Test
 * @package CommonTest\Service
 */
class ApplicationOperatingCentreTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\Application
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp(): void
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->sut = new ApplicationOperatingCentre();

    }

    public function testGetId()
    {
        $id = 1;
        $mockApplicationService = m::mock('Common\Service\Data\Application');
        $mockApplicationService
            ->shouldReceive('getId')
            ->once()
            ->andReturn($id);
        $this->sut->setApplicationService($mockApplicationService);

        $this->assertEquals($id, $this->sut->getId());
    }

    public function testFetchListOptionsFullAddress()
    {

        $context = 'application';
        $useGroups = false;

        $mockData = [
            'operatingCentres' => [
                0 => [
                    'operatingCentre' => [
                        'id' => 99,
                        'address' => [
                            'addressLine1' => 'a1',
                            'addressLine2' => 'a2',
                            'addressLine3' => 'a3',
                            'addressLine4' => 'a4',
                            'town' => 'anytown',
                            'postcode' => 'pc'
                        ]
                    ]
                ]
            ]
        ];

        $id = 1;
        $mockApplicationService = m::mock('Common\Service\Data\Application');

        $mockApplicationService
            ->shouldReceive('getId')
            ->andReturn($id);

        $mockApplicationService
            ->shouldReceive('fetchOperatingCentreData')
            ->with($id)
            ->andReturn($mockData);

        $this->sut->setApplicationService($mockApplicationService);

        $output = $this->sut->fetchListOptions($context, $useGroups);

        $this->assertEquals([99 => 'a1, a2, a3, a4, anytown, pc'], $output);
    }

    public function testFetchListOptionsPartialAddress()
    {

        $context = 'application';
        $useGroups = false;

        $mockData = [
            'operatingCentres' => [
                0 => [
                    'operatingCentre' => [
                        'id' => 99,
                        'address' => [
                            'addressLine1' => 'a1',
                            'addressLine2' => 'a2',
                            'addressLine3' => 'a3',
                            'addressLine4' => 'a4',
                            'town' => 'anytown',
                            'postcode' => 'pc'
                        ]
                    ]
                ]
            ]
        ];

        $id = 1;
        $mockApplicationService = m::mock('Common\Service\Data\Application');

        $mockApplicationService
            ->shouldReceive('getId')
            ->andReturn($id);

        $mockApplicationService
            ->shouldReceive('fetchOperatingCentreData')
            ->with($id)
            ->andReturn($mockData);

        $this->sut->setApplicationService($mockApplicationService);
        $this->sut->setOutputType(ApplicationOperatingCentre::OUTPUT_TYPE_PARTIAL);

        $output = $this->sut->fetchListOptions($context, $useGroups);

        $this->assertEquals([99 => 'a1, anytown'], $output);
    }
}
