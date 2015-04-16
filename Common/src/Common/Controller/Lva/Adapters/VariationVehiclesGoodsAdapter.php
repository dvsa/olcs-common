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
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->formatFilters($query);

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForVariation($id, $filters);
    }

    public function getFormData($id)
    {
        return [];
    }
}
