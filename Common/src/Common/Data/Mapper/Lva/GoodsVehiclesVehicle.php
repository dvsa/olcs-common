<?php

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Table\Formatter\VehicleDiscNo;

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehiclesVehicle implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        $licenceVehicle = $data;
        unset($licenceVehicle['vehicle']);

        $licenceVehicle['discNo'] = VehicleDiscNo::format($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        $dataFieldset = $data['vehicle'];
        $dataFieldset['version'] = $data['version'];

        return [
            'licence-vehicle' => $licenceVehicle,
            'data' => $dataFieldset
        ];
    }
}
