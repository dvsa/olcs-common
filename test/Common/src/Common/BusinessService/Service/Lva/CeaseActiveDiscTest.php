<?php

/**
 * Cease Active Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\CeaseActiveDisc;
use Common\BusinessService\Response;

/**
 * Cease Active Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CeaseActiveDiscTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new CeaseActiveDisc();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithNoCeasedDisc()
    {
        $params = [
            'id' => 111
        ];

        $results = [
            'goodsDiscs' => [
                [
                    'id' => 222,
                    'ceasedDate' => null
                ]
            ]
        ];

        // Mocks
        $mockLicenceVehicle = m::mock();
        $mockDate = m::mock();
        $mockGoodsDisc = m::mock();

        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);
        $this->sm->setService('Entity\GoodsDisc', $mockGoodsDisc);
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockLicenceVehicle->shouldReceive('getActiveDiscs')
            ->with(111)
            ->andReturn($results);

        $mockDate->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2014-01-01 10:10:10');

        $mockGoodsDisc->shouldReceive('save')
            ->with(['id' => 222,'ceasedDate' => '2014-01-01 10:10:10']);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function testProcessWithCeasedDisc()
    {
        $params = [
            'id' => 111
        ];

        $results = [
            'goodsDiscs' => [
                [
                    'id' => 222,
                    'ceasedDate' => '2011-01-01 10:10:10'
                ]
            ]
        ];

        // Mocks
        $mockLicenceVehicle = m::mock();

        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        // Expectations
        $mockLicenceVehicle->shouldReceive('getActiveDiscs')
            ->with(111)
            ->andReturn($results);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function testProcessWithNoDiscs()
    {
        $params = [
            'id' => 111
        ];

        $results = [
            'goodsDiscs' => []
        ];

        // Mocks
        $mockLicenceVehicle = m::mock();

        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        // Expectations
        $mockLicenceVehicle->shouldReceive('getActiveDiscs')
            ->with(111)
            ->andReturn($results);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}
