<?php

/**
 * UnlicensedVehiclePsvTypeTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;


use Common\Service\Table\Formatter\UnlicensedVehiclePsvType;

/**
 * Class UnlicensedVehiclePsvTypeTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class UnlicensedVehiclePsvTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $column = [
            'title' => 'some.translation.key',
            'stack' => 'vehicle->psvType->id',
            'formatter' => 'UnlicensedVehiclePsvType',
            'name' => 'type',
        ];

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn(
                m::mock()
                    ->shouldReceive('translate')
                    ->andReturnUsing(
                        function ($key) {
                            $map = [
                                'internal-operator-unlicensed-vehicles.type.vhl_t_a' => 'small',
                                'internal-operator-unlicensed-vehicles.type.vhl_t_b' => 'medium',
                                'internal-operator-unlicensed-vehicles.type.vhl_t_c' => 'large',
                            ];
                            return isset($map[$key]) ? $map[$key] : '';
                        }
                    )
                    ->getMock()
            )
            ->shouldReceive('get')
            ->with('Helper\Stack')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getStackValue')
                    ->with(
                        $data,
                        ['vehicle', 'psvType', 'id']
                    )
                    ->andReturn($data['vehicle']['psvType']['id'])
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals($expected, UnlicensedVehiclePsvType::format($data, $column, $sm));
    }

    public function formatProvider()
    {
        return [
            'null' => [
                [
                    'vehicle' => [
                        'psvType' => null,
                    ],
                ],
                '',
            ],
            'small' => [
                [
                    'vehicle' => [
                        'psvType' => [
                            'id' => 'vhl_t_a',
                        ],
                    ],
                ],
                'small',
            ],
            'medium' => [
                [
                    'vehicle' => [
                        'psvType' => [
                            'id' => 'vhl_t_b',
                        ],
                    ],
                ],
                'medium',
            ],
            'large' => [
                [
                    'vehicle' => [
                        'psvType' => [
                            'id' => 'vhl_t_c',
                        ],
                    ],
                ],
                'large',
            ],
        ];
    }
}
