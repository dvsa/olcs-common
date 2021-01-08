<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\OcContextListDataService;
use Mockery as m;

/**
 * Class OcContextListDataService Test
 * @package CommonTest\Service
 */
class OcContextListDataServiceTest extends MockeryTestCase
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
        $this->sut = new OcContextListDataService();
    }

    /**
     * @group data_service
     * @group oc_context_list_data_service
     */
    public function testCreateService()
    {
        $service = $this->sut->createService($this->serviceManager);

        $this->assertSame($service, $this->sut);
    }

    public function testFetchListOptionsApplicationContext()
    {
        $useGroups = false;

        $applicationOperatingCentres = [
            'operatingCentres' => [
                0 => [],
                1 => []
            ]
        ];

        $mockApplicationOperatingCentre = m::mock('Common\Service\Data\ApplicationOperatingCentre');
        $mockApplicationOperatingCentre->shouldReceive('fetchListOptions')->andReturn($applicationOperatingCentres);

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Common\Service\Data\ApplicationOperatingCentre')->andReturn($mockApplicationOperatingCentre);

        $this->sut->setServiceLocator($mockSl);
        $appOptions = $this->sut->fetchListOptions('application', $useGroups);

        $this->assertEquals($applicationOperatingCentres, $appOptions);
    }

    public function testFetchListOptionsLicenceContext()
    {
        $useGroups = false;

        $licenceOperatingCentres = [
            'operatingCentres' => [
                0 => []
            ]
        ];

        $mockLicenceOperatingCentre = m::mock('Common\Service\Data\LicenceOperatingCentre');
        $mockLicenceOperatingCentre->shouldReceive('fetchListOptions')->andReturn($licenceOperatingCentres);

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Common\Service\Data\LicenceOperatingCentre')->andReturn($mockLicenceOperatingCentre);

        $this->sut->setServiceLocator($mockSl);

        $lOptions = $this->sut->fetchListOptions('licence', $useGroups);

        $this->assertEquals($licenceOperatingCentres, $lOptions);
    }

    public function testFetchListOptionsNullContext()
    {
        $useGroups = false;

        $options = $this->sut->fetchListOptions('', $useGroups);

        $this->assertEmpty($options);
    }
}
