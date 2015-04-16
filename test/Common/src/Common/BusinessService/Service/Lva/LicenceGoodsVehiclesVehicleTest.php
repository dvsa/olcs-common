<?php

/**
 * Licence Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\LicenceGoodsVehiclesVehicle;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Licence Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    protected $sm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new LicenceGoodsVehiclesVehicle();
        $this->sut->setBusinessServiceManager($this->bsm);
        $this->sut->setBusinessRuleManager($this->brm);
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithAdd()
    {
        // <<--- START OF parent::process
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
        $mockLicenceGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('GoodsVehiclesVehicle', $mockGoodsVehiclesVehicleRule);
        $this->brm->setService('LicenceGoodsVehiclesLicenceVehicle', $mockLicenceGoodsVehiclesLicenceVehicle);

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

        $mockLicenceGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->with(['foo' => 'bar'], 'add', 333, 222, 111)
            ->andReturn($validatedLicenceVehicleData);

        $mockLicenceVehicle->shouldReceive('save')
            ->with($validatedLicenceVehicleData)
            ->andReturn(['id' => 444]);
        // <<--- END OF parent::process

        // <<--- START OF sut->process

        // Mocks
        $mockRequestDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\RequestDisc', $mockRequestDisc);

        $requestResponse = m::mock();

        // Expectations
        $mockRequestDisc->shouldReceive('process')
            ->with(['licenceVehicle' => 444, 'isCopy' => 'N'])
            ->andReturn($requestResponse);

        $requestResponse->shouldReceive('isOk')
            ->andReturn(true);

        // <<--- END OF sut->process

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function testProcessWithAddWithFail()
    {
        // <<--- START OF parent::process
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
        $mockLicenceGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('GoodsVehiclesVehicle', $mockGoodsVehiclesVehicleRule);
        $this->brm->setService('LicenceGoodsVehiclesLicenceVehicle', $mockLicenceGoodsVehiclesLicenceVehicle);

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

        $mockLicenceGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->with(['foo' => 'bar'], 'add', 333, 222, 111)
            ->andReturn($validatedLicenceVehicleData);

        $mockLicenceVehicle->shouldReceive('save')
            ->with($validatedLicenceVehicleData)
            ->andReturn(['id' => 444]);
        // <<--- END OF parent::process

        // <<--- START OF sut->process

        // Mocks
        $mockRequestDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\RequestDisc', $mockRequestDisc);

        $requestResponse = m::mock();

        // Expectations
        $mockRequestDisc->shouldReceive('process')
            ->with(['licenceVehicle' => 444, 'isCopy' => 'N'])
            ->andReturn($requestResponse);

        $requestResponse->shouldReceive('isOk')
            ->andReturn(false);

        // <<--- END OF sut->process

        $response = $this->sut->process($params);

        $this->assertSame($requestResponse, $response);
    }

    public function testProcessWithEdit()
    {
        // <<--- START OF parent::process
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
        $mockLicenceGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');

        $this->brm->setService('GoodsVehiclesVehicle', $mockGoodsVehiclesVehicleRule);
        $this->brm->setService('LicenceGoodsVehiclesLicenceVehicle', $mockLicenceGoodsVehiclesLicenceVehicle);

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

        $mockLicenceGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->with(['id' => 444, 'foo' => 'bar'], 'edit', 333, 222, 111)
            ->andReturn($validatedLicenceVehicleData);

        $mockLicenceVehicle->shouldReceive('save')
            ->with($validatedLicenceVehicleData)
            ->andReturn(null);
        // <<--- END OF parent::process

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}
