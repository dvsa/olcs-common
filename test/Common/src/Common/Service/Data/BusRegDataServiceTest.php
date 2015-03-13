<?php

namespace OlcsTest\Service\Data;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\BusReg;
use Mockery as m;

/**
 * Class BusReg Data Service Test
 * @package CommonTest\Service
 */
class BusRegDataServiceTest extends MockeryTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\BusReg
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->sut = new BusReg();
    }

    public function testGetServiceName()
    {
        $this->assertEquals('BusReg', $this->sut->getServiceName());
    }

    /**
     * @group data_service
     * @group bus_reg_data_service
     */
    public function testFetchDetail()
    {
        $id = 1;
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockData = ['id' => 99];

        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('/' . $id, m::type('array'))
            ->andReturn($mockData);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->fetchDetail($id);

        $this->assertEquals($mockData, $result);
    }

    /**
     * @group data_service
     * @group bus_reg_data_service
     */
    public function testFetchVariatonHistory()
    {
        $params['routeNo'] = '123';
        $params['sort'] = 'variationNo';
        $params['order'] = 'DESC';

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockData = [ 0 => ['id' => 99]];

        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', m::type('array'))
            ->andReturn(['Results' => $mockData, 'Count' => count($mockData)]);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->fetchVariationHistory($params);

        $this->assertEquals($mockData, $result);
    }
}
