<?php

/**
 * Variation Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehiclesAdapterInterface;

/**
 * Variation Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvAdapter extends AbstractAdapter implements VehiclesAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * Here we can just wrap the application version
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('ApplicationVehiclesPsvAdapter')->getVehiclesData($id);
    }
}
