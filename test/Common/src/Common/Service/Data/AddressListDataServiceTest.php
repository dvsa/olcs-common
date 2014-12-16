<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\AddressListDataService;
use Mockery as m;

/**
 * Class AddressListService Test
 * @package CommonTest\Service
 */
class AddressListDataServiceTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\CategoryDataService
     */
    private $sut;

    private $serviceManager;

    private $mockRestHelper;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $mockLicenceService = m::mock('\Common\Service\Data\Licence');

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Common\Service\Data\Licence', $mockLicenceService);

        $this->sut = new AddressListDataService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testCreateService()
    {
        $service = $this->sut->createService($this->serviceManager);

        $this->assertSame($service, $this->sut);
    }

    public function testFetchListOptions()
    {
        $context = ['services' => ['licence']];
        $useGroups = false;

        $addressData = [
            0 => [
                'id' => 99,
                'addressLine1' => '101 Some street'
            ]
        ];
        $mockLicenceService = m::mock('\Common\Service\Data\Licence');
        $mockLicenceService->shouldReceive('fetchAddressListData')->withAnyArgs()
            ->andReturn($addressData);

        $mockDataServiceManager = m::mock('Common\Service\Data\PluginManager');
        $mockDataServiceManager->shouldReceive('get')->with('Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $mockSL = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('get')->with('DataServiceManager')->andReturn($mockDataServiceManager);
        $this->sut->setServiceLocator($mockSL);

        $output = $this->sut->fetchListOptions($context, $useGroups);

        $this->assertEquals([99 => '101 Some street'], $output);
    }

    /**
     * @expectedException \LogicException
     */
    public function testFetchListOptionsInvalidDataService()
    {
        $context = ['services' => ['licence']];
        $useGroups = false;

        $addressData = [
            0 => [
                'id' => 99,
                'addressLine1' => '101 Some street'
            ]
        ];
        $mockLicenceService = m::mock('\Common\Service\Data\SomeEntity');
        $mockLicenceService->shouldReceive('fetchAddressListData')->withAnyArgs()
            ->andReturn($addressData);

        $mockDataServiceManager = m::mock('Common\Service\Data\PluginManager');
        $mockDataServiceManager->shouldReceive('get')->with('Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $mockSL = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('get')->with('DataServiceManager')->andReturn($mockDataServiceManager);
        $this->sut->setServiceLocator($mockSL);

        $output = $this->sut->fetchListOptions($context, $useGroups);
    }
}
