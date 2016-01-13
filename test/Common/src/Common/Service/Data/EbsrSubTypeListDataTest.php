<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\EbsrSubTypeListDataService;
use Common\Service\Data\RefData as RefDataService;
use Mockery as m;

/**
 * Class EbsrSubTypeListDataService
 * @package OlcsTest\Service\Data
 */
class EbsrSubTypeListDataServiceTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\EbsrSubTypeListDataService
     */
    private $sut;

    private $serviceManager;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $mockRefDataService = m::mock('\Common\Service\Data\RefData');

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Common\Service\Data\RefData', $mockRefDataService);

        $this->sut = new EbsrSubTypeListDataService();
    }

    public function testCreateService()
    {
        $service = $this->sut->createService($this->serviceManager);

        $this->assertSame($service, $this->sut);
        $this->assertInstanceOf('Common\Service\Data\RefData', $this->sut->getRefDataService());
    }

    public function testFetchListOptions()
    {
        $validListOptions = [
            0 => [
                'id' => 'ebsrt_new',
                'description' => 'NEW'
            ],
            1 => [
                'id' => 'ebsrt_refresh',
                'description' => 'REFRESH'
            ],
            2 => [
                'id' => 'ebsrt_someother',
                'description' => 'SOMEOTHER'
            ]
        ];
        $mockRefDataService = m::mock('Common\Service\Data\RefData');
        $mockRefDataService->shouldReceive('fetchListData')->andReturn($validListOptions);
        $this->sut->setRefDataService($mockRefDataService);

        $result = $this->sut->fetchListOptions();

        $this->assertCount(2, $result);
    }
}
