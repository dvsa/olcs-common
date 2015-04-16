<?php

/**
 * Application Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\ApplicationGoodsVehiclesVehicle;
use Common\BusinessService\Response;

/**
 * Applications Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $brm;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationGoodsVehiclesVehicle();

        $this->sm = Bootstrap::getServiceManager();

        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut->setBusinessRuleManager($this->brm);
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithAdd()
    {
        $params = [
            'mode' => 'add',
            'id' => 111,
            'licenceId' => 222,
            'data' => [
                'foo' => 'bar'
            ]
        ];

        $validatedData = [
            'licence-vehicle' => [
                'foo' => 'bar'
            ],
            'bar' => 'foo'
        ];

        $validatedLicenceVehicleData = [
            'foo' => 'cake'
        ];

        // Mocks
        $mockGoodsVehiclesVehicleRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockApplicationGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('GoodsVehiclesVehicle', $mockGoodsVehiclesVehicleRule);
        $this->brm->setService('ApplicationGoodsVehiclesLicenceVehicle', $mockApplicationGoodsVehiclesLicenceVehicle);

        $mockVehicle = m::mock();
        $mockLicenceVehicle = m::mock();

        $this->sm->setService('Entity\Vehicle', $mockVehicle);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        // Expectations
        $mockGoodsVehiclesVehicleRule->shouldReceive('validate')
            ->with(['foo' => 'bar'], 'add')
            ->andReturn($validatedData);

        $mockVehicle->shouldReceive('save')
            ->with(['bar' => 'foo'])
            ->andReturn(['id' => 333]);

        $mockApplicationGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->with(['foo' => 'bar'], 'add', 333, 222, 111)
            ->andReturn($validatedLicenceVehicleData);

        $mockLicenceVehicle->shouldReceive('save')
            ->with($validatedLicenceVehicleData)
            ->andReturn(['id' => 444]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['licenceVehicleId' => 444], $response->getData());
    }

    public function testProcessWithEdit()
    {
        $params = [
            'mode' => 'edit',
            'id' => 111,
            'licenceId' => 222,
            'data' => [
                'id' => 333,
                'foo' => 'bar'
            ]
        ];

        $validatedData = [
            'licence-vehicle' => [
                'id' => 444,
                'foo' => 'bar'
            ],
            'id' => 333,
            'bar' => 'foo'
        ];

        $validatedLicenceVehicleData = [
            'id' => 444,
            'foo' => 'cake'
        ];

        // Mocks
        $mockGoodsVehiclesVehicleRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $mockApplicationGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('GoodsVehiclesVehicle', $mockGoodsVehiclesVehicleRule);
        $this->brm->setService('ApplicationGoodsVehiclesLicenceVehicle', $mockApplicationGoodsVehiclesLicenceVehicle);

        $mockVehicle = m::mock();
        $mockLicenceVehicle = m::mock();

        $this->sm->setService('Entity\Vehicle', $mockVehicle);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        // Expectations
        $mockGoodsVehiclesVehicleRule->shouldReceive('validate')
            ->with(['id' => 333, 'foo' => 'bar'], 'edit')
            ->andReturn($validatedData);

        $mockVehicle->shouldReceive('save')
            ->with(['id' => 333, 'bar' => 'foo'])
            ->andReturn(null);

        $mockApplicationGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->with(['id' => 444, 'foo' => 'bar'], 'edit', 333, 222, 111)
            ->andReturn($validatedLicenceVehicleData);

        $mockLicenceVehicle->shouldReceive('save')
            ->with($validatedLicenceVehicleData)
            ->andReturn(null);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['licenceVehicleId' => 444], $response->getData());
    }
}
