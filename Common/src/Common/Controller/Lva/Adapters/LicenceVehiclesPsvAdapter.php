<?php

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehiclesAdapterInterface;

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesPsvAdapter extends AbstractAdapter implements VehiclesAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesPsvData($id);
    }
}
