<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\ApplicationOperatingCentre;
use Common\Service\Entity\ApplicationEntityService;
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
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->sut = new ApplicationOperatingCentre();

    }

    public function testGetServiceName()
    {
        $this->assertEquals('ApplicationOperatingCentre', $this->sut->getServiceName());
    }

    public function testGetBundle()
    {
        $bundle = $this->sut->getBundle();
        $this->assertInternalType('array', $bundle);
        $this->assertArrayHasKey('operatingCentres', $bundle['children']);
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
            ->with($id, m::type('array'))
            ->andReturn($mockData);

        $this->sut->setApplicationService($mockApplicationService);

        $output = $this->sut->fetchListOptions($context, $useGroups);

        $this->assertEquals([99 => 'a1, a2, a3 a4 pc'], $output);
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
            ->with($id, m::type('array'))
            ->andReturn($mockData);

        $this->sut->setApplicationService($mockApplicationService);
        $this->sut->setOutputType(ApplicationOperatingCentre::OUTPUT_TYPE_PARTIAL);

        $output = $this->sut->fetchListOptions($context, $useGroups);

        $this->assertEquals([99 => 'a1, anytown'], $output);
    }
}
