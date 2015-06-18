<?php

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Vehicles Goods Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapter extends AbstractAdapter
{
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->formatFilters($query);

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForLicence($id, $filters);
    }
}
