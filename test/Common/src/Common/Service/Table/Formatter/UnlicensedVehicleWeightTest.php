<?php

/**
 * UnlicensedVehicleWeightTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\UnlicensedVehicleWeight;

/**
 * Class UnlicensedVehicleWeightTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class UnlicensedVehicleWeightTest extends TestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $column = [
            'title' => 'some.translation.key',
            'stack' => 'vehicle->platedWeight',
            'formatter' => 'UnlicensedVehicleWeight',
            'name' => 'weight',
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Stack')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getStackValue')
                    ->with(
                        $data,
                        ['vehicle', 'platedWeight']
                    )
                    ->andReturn($data['vehicle']['platedWeight'])
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals($expected, UnlicensedVehicleWeight::format($data, $column, $sm));
    }

    public function formatProvider()
    {
        return [
            'empty weight' => [
                [
                    'vehicle' => [
                        'platedWeight' => null,
                    ],
                ],
                '',
            ],
            'weight specified' => [
                [
                    'vehicle' => [
                        'platedWeight' => 99,
                    ],
                ],
                '99 kg',
            ],
        ];
    }
}
