<?php

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\GoodsVehicles;

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'version' => 1,
            'hasEnteredReg' => 'N'
        ];

        $output = GoodsVehicles::mapFromResult($input);

        $expected = [
            'data' => [
                'version' => 1,
                'hasEnteredReg' => 'N'
            ]
        ];

        $this->assertEquals($expected, $output);
    }

    public function testMapFromResult2()
    {
        $input = [
            'version' => 1,
            'hasEnteredReg' => 'Y'
        ];

        $output = GoodsVehicles::mapFromResult($input);

        $expected = [
            'data' => [
                'version' => 1,
                'hasEnteredReg' => 'Y'
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}
