<?php

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapter extends AbstractAdapter implements VehicleGoodsAdapterInterface
{
    public function getFormData($id)
    {
        return [];
    }

    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getVehiclesData($id);
    }

    /**
     * Get all relevant form filters
     *
     * Here we wrap the application version and override
     * the specified date
     */
    public function getFilters($params)
    {
        return array_merge(
            $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilters($params),
            ['specified' => 'Y']
        );
    }
}
