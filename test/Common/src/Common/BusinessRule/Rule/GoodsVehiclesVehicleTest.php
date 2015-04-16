<?php

/**
 * Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessRule\Rule\GoodsVehiclesVehicle;

/**
 * Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new GoodsVehiclesVehicle();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($input, $mode, $expected)
    {
        $expectedDataMap = [
            'main' => [
                'mapFrom' => [
                    'data'
                ],
                'children' => [
                    'licence-vehicle' => [
                        'mapFrom' => [
                            'licence-vehicle'
                        ]
                    ]
                ]
            ]
        ];

        // Mocks
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        // Expectations
        $mockDataHelper->shouldReceive('processDataMap')
            ->with($input, $expectedDataMap)
            ->andReturn($input);

        $this->assertEquals($expected, $this->sut->validate($input, $mode));
    }

    public function providerValidate()
    {
        return [
            'add' => [
                [
                    'foo' => 'bar',
                    'vrm' => '234'
                ],
                'add',
                [
                    'foo' => 'bar',
                    'vrm' => '234'
                ]
            ],
            'edit' => [
                [
                    'foo' => 'bar',
                    'vrm' => '234'
                ],
                'edit',
                [
                    'foo' => 'bar'
                ]
            ]
        ];
    }
}
