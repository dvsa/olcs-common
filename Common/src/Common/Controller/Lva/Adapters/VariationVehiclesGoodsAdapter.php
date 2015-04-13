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
        $query['specified'] = 'Y';

        return $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->getFilteredVehiclesData($id, $query);
    }

    public function getFormData($id)
    {
        return [];
    }
}
