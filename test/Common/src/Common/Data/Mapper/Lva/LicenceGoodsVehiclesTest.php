<?php

/**
 * Licence Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\LicenceGoodsVehicles;

/**
 * Licence Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $data = [
            'organisation' => [
                'confirmShareVehicleInfo' => 'Y'
            ]
        ];

        $expected = [
            'shareInfo' => [
                'shareInfo' => 'Y'
            ]
        ];

        $this->assertEquals($expected, LicenceGoodsVehicles::mapFromResult($data));
    }
}
