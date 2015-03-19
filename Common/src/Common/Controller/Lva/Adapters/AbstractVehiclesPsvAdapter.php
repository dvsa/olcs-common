<?php

/**
 * Abstract Vehicles Psv Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehiclesAdapterInterface;

/**
 * Abstract Vehicles Psv Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractVehiclesPsvAdapter extends AbstractAdapter implements VehiclesAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    abstract public function getVehiclesData($id);

    abstract public function warnIfAuthorityExceeded($id, $types, $redirecting);

    /**
     * Get count of vehicles of a particular type
     * @param int $id
     * @param string $psvType (VehicleEntityService::PSV_TYPE_SMALL|PSV_TYPE_MEDIUM|PSV_TYPE_LARGE)
     * @return int|null
     */
    public function getVehicleCountByPsvType($id, $psvType)
    {
        $count = 0;
        $licenceVehicles = $this->getVehiclesData($id);

        foreach ($licenceVehicles as $licenceVehicle) {
            if ($licenceVehicle['vehicle']['psvType']['id'] == $psvType) {
                $count++;
            }
        }

        return $count;
    }

}
