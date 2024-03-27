<?php

/**
 * Licence Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;

/**
 * Licence Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehicles implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        return [
            'shareInfo' => [
                'shareInfo' => $data['organisation']['confirmShareVehicleInfo']
            ]
        ];
    }
}
