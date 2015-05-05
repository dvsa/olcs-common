<?php

/**
 * Goods Vehicles Removed Vehicle Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Goods Vehicles Removed Vehicle Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceGoodsVehiclesRemovedVehicleTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock('Common\BusinessRule\Rule\LicenceGoodsVehiclesRemovedVehicle')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($data, $checkDate, $expected)
    {
        $this->sut
            ->shouldReceive('getBusinessRuleManager')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('CheckDate')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('validate')
                    ->with($data['removalDate'])
                    ->andReturn($checkDate)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once();

        $this->assertEquals($expected, $this->sut->validate($data));
    }

    public function providerValidate()
    {
        return [
            [
                [
                    'id' => 1,
                    'version' => 2,
                    'removalDate' => [
                        'day' => '01',
                        'month' => '02',
                        'year' => '2015'
                    ]
                ],
                'removalDate' => [
                    'day' => '01',
                    'month' => '02',
                    'year' => '2015'
                ],
                [
                    'id' => 1,
                    'version' => 2,
                    'removalDate' => [
                        'day' => '01',
                        'month' => '02',
                        'year' => '2015'
                    ]
                ]
            ],
            [
                [
                    'id' => 1,
                    'version' => 2,
                    'removalDate' => []
                ],
                null,
                null
            ]
        ];
    }
}
