<?php

/**
 * Variation Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\VehicleGoodsAdapterInterface;

/**
 * Variation Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesGoodsAdapter extends AbstractAdapter implements VehicleGoodsAdapterInterface
{
    public function save($data, $id)
    {
    }

    public function getFormData($id)
    {
        return [];
    }

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
        return $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getVehiclesData($id);
    }

    /**
     * Do we need to show filters for vehciles
     */
    public function showFilters()
    {
        return true;
    }

    /**
     * Retrieve the filter form
     *
     * Here we can just wrap the application version
     */
    public function getFilterForm()
    {
        return $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilterForm();
    }

    /**
     * Get all relevant form filters
     *
     * Here we can just wrap the application version
     */
    public function getFilters($params)
    {
        return $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilters($params);
    }
}
