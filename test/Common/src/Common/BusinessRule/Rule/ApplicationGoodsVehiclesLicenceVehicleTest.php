<?php

/**
 * Application Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessRule\Rule\ApplicationGoodsVehiclesLicenceVehicle;

/**
 * Application Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesLicenceVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $brm;

    public function setUp()
    {
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new ApplicationGoodsVehiclesLicenceVehicle();

        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testValidate()
    {
        $data = ['foo' => 'bar'];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $applicationId = 333;

        // Mocks
        $mockVariationGoodsVehiclesLicenceVehicle = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('VariationGoodsVehiclesLicenceVehicle', $mockVariationGoodsVehiclesLicenceVehicle);

        // Expecations
        $mockVariationGoodsVehiclesLicenceVehicle->shouldReceive('validate')
            ->once()
            ->with($data, $mode, $vehicleId, $licenceId, $applicationId)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->validate($data, $mode, $vehicleId, $licenceId, $applicationId));
    }
}
