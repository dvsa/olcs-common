<?php

/**
 * Licence Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessRule\Rule\LicenceGoodsVehiclesLicenceVehicle;
use CommonTest\Bootstrap;

/**
 * Licence Goods Vehicles Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesLicenceVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $brm;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new LicenceGoodsVehiclesLicenceVehicle();

        $this->sut->setBusinessRuleManager($this->brm);
        $this->sut->setServiceLocator($this->sm);
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
        $id = 333;
        $expected = [
            'foo' => 'bar',
            'vehicle' => 111,
            'licence' => 222
        ];

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }

    public function testValidateMinimalEdit()
    {
        // Data
        $data = [
            'foo' => 'bar',
            'removedDate' => 'foo',
            'discNo' => 'bar'
        ];
        $mode = 'edit';
        $vehicleId = 111;
        $licenceId = 222;
        $id = 333;
        $expected = [
            'foo' => 'bar',
            'vehicle' => 111
        ];

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

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
        $id = 333;
        $expected = [
            'foo' => 'bar',
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

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

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
        $id = 333;
        $expected = [
            'receivedDate' => '2014-01-01',
            'foo' => 'bar',
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

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }

    public function testValidateInvalidSpecifiedDate()
    {
        // Data
        $data = [
            'specifiedDate' => [
                'day' => '0',
                'month' => '0',
                'year' => '0'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $id = 333;
        $expected = [
            'specifiedDate' => '2014-01-01',
            'foo' => 'bar',
            'vehicle' => 111,
            'licence' => 222
        ];

        // Mocks
        $mockCheckDate = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('CheckDate', $mockCheckDate);

        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        // Expectations
        $mockCheckDate->shouldReceive('validate')
            ->with(['day' => '0', 'month' => '0', 'year' => '0'])
            ->andReturn(null);

        $mockDateHelper->shouldReceive('getDate')
            ->with('Y-m-d')
            ->andReturn('2014-01-01');

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }

    public function testValidateValidSpecifiedDate()
    {
        // Data
        $data = [
            'specifiedDate' => [
                'day' => '01',
                'month' => '01',
                'year' => '2014'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $id = 333;
        $expected = [
            'specifiedDate' => '2014-01-01',
            'foo' => 'bar',
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

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }

    public function testValidateInvalidRemovalDate()
    {
        // Data
        $data = [
            'removalDate' => [
                'day' => '0',
                'month' => '0',
                'year' => '0'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $id = 333;
        $expected = [
            'foo' => 'bar',
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

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }

    public function testValidateValidRemovalDate()
    {
        // Data
        $data = [
            'removalDate' => [
                'day' => '01',
                'month' => '01',
                'year' => '2014'
            ],
            'foo' => 'bar'
        ];
        $mode = 'add';
        $vehicleId = 111;
        $licenceId = 222;
        $id = 333;
        $expected = [
            'removalDate' => '2014-01-01',
            'foo' => 'bar',
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

        $response = $this->sut->validate($data, $mode, $vehicleId, $licenceId, $id);

        $this->assertEquals($expected, $response);
    }
}
