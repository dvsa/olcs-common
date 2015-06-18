<?php

/**
 * Variation Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesGoodsAdapter extends AbstractAdapter
{
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->formatFilters($query);

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForVariation($id, $filters);
    }
}
