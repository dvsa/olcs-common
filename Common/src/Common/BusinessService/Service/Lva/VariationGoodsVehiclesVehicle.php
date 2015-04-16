<?php

/**
 * Variation Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

/**
 * Variation Goods Vehicles Vehicle
 * - Shares functionality with ApplicationGoodsVehiclesVehicle service, only difference is the name of one of the
 *  business rules
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehiclesVehicle extends ApplicationGoodsVehiclesVehicle
{
    protected $licenceVehicleRule = 'VariationGoodsVehiclesLicenceVehicle';
}
