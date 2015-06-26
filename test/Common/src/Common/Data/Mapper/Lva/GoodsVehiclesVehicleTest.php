<?php

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\GoodsVehiclesVehicle;

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesVehicleTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'bar' => 'foo',
            'version' => 1,
            'vehicle' => [
                'foo' => 'bar'
            ],
            'goodsDiscs' => [
                [
                    'discNo' => 1234
                ]
            ]
        ];

        $output = GoodsVehiclesVehicle::mapFromResult($input);

        $expected = [
            'licence-vehicle' => [
                'bar' => 'foo',
                'version' => 1,
                'discNo' => 'Pending'
            ],
            'data' => [
                'foo' => 'bar',
                'version' => 1
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}
