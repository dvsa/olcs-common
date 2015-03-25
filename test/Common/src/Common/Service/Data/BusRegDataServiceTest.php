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
        $regNo = '123';

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockData = [ 0 => ['id' => 99]];

        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', m::type('array'))
            ->andReturn(['Results' => $mockData, 'Count' => count($mockData)]);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->fetchVariationHistory($regNo);

        $this->assertEquals($mockData, $result);
    }

    /**
     * @group data_service
     * @group bus_reg_data_service
     */
    public function testFetchLatestActiveVariation()
    {
        $regNo = '123';

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockData = [ 0 => ['id' => 99]];

        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', m::type('array'))
            ->andReturn(['Results' => $mockData, 'Count' => count($mockData)]);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->fetchLatestActiveVariation($regNo);

        $this->assertEquals($mockData[0], $result);
    }

    /**
     * @dataProvider isLatestVariationDataProvider
     * @param array $data
     * @param array $latestActiveVariationData
     * @param bool $expectedResult
     * @group data_service
     * @group bus_reg_data_service
     */
    public function testIsLatestVariation($data, $latestActiveVariationData, $expectedResult)
    {
        $this->sut->setData($data['id'], $data);

        $mockRestClient = m::mock('Common\Util\RestClient');

        $mockRestClient
            ->shouldReceive('get')
            ->with('', m::type('array'))
            ->andReturn(['Results' => [$latestActiveVariationData], 'Count' => 1]);

        $this->sut->setRestClient($mockRestClient);

        $result = $this->sut->isLatestVariation($data['id']);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for isLatestVariation.
     *
     * @return array
     */
    public function isLatestVariationDataProvider()
    {
        return [
            // bus reg without regNo
            [
                ['id' => 10],
                [],
                true
            ],
            // no active variation for the regNo
            [
                ['id' => 10, 'regNo' => '123'],
                [],
                true
            ],
            // active variation for the regNo matches the one to be checked
            [
                ['id' => 10, 'regNo' => '123'],
                ['id' => 10],
                true
            ],
            // active variation for the regNo does not match the one to be checked
            [
                ['id' => 10, 'regNo' => '123'],
                ['id' => 9],
                false
            ],
        ];
    }
}
