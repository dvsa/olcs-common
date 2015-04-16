<?php

/**
 * Variation Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessRule\Rule\VariationGoodsVehiclesLicenceVehicle;

/**
 * Variation Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehiclesLicenceVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $brm;

    public function setUp()
    {
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new VariationGoodsVehiclesLicenceVehicle();

        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testValidateMinimalAdd()
    {
        // Data
        $data = [
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $applicationId = 333;
        $expected = [
            'foo' => 'bar',
            'application' => 333,
            'vehicle' => 111,
            'licence' => 222
        ];

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $applicationId);

        $this->assertEquals($expected, $response);
    }

    public function testValidateMinimalEdit()
    {
        // Data
        $data = [
            'foo' => 'bar',
            'specifiedDate' => 'foo',
            'removedDate' => 'foo',
            'discNo' => 'foo'
        ];
        $mode = 'edit';
        $vehicleId = 111;
        $licenceId = 222;
        $applicationId = 333;
        $expected = [
            'foo' => 'bar',
            'application' => 333,
            'vehicle' => 111
        ];

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $applicationId);

        $this->assertEquals($expected, $response);
    }

    public function testValidateInvalidReceivedDate()
    {
        // Data
        $data = [
            'receivedDate' => [
                'day' => '0',
                'month' => '0',
                'year' => '0'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $applicationId = 333;
        $expected = [
            'foo' => 'bar',
            'application' => 333,
            'vehicle' => 111,
            'licence' => 222
        ];

        // Mocks
        $mockCheckDate = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('CheckDate', $mockCheckDate);

        // Expectations
        $mockCheckDate->shouldReceive('validate')
            ->with(['day' => '0', 'month' => '0', 'year' => '0'])
            ->andReturn(null);

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $applicationId);

        $this->assertEquals($expected, $response);
    }

    public function testValidateValidReceivedDate()
    {
        // Data
        $data = [
            'receivedDate' => [
                'day' => '01',
                'month' => '01',
                'year' => '2014'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $applicationId = 333;
        $expected = [
            'receivedDate' => '2014-01-01',
            'foo' => 'bar',
            'application' => 333,
            'vehicle' => 111,
            'licence' => 222
        ];

        // Mocks
        $mockCheckDate = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('CheckDate', $mockCheckDate);

        // Expectations
        $mockCheckDate->shouldReceive('validate')
            ->with(['day' => '01', 'month' => '01', 'year' => '2014'])
            ->andReturn('2014-01-01');

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $applicationId);

        $this->assertEquals($expected, $response);
    }
}
