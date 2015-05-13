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
    public function getFilteredVehiclesData($id, $query)
    {
        $filters = $this->getServiceLocator()->get('ApplicationVehiclesGoodsAdapter')->formatFilters($query);

        return $this->getServiceLocator()->get('Entity\LicenceVehicle')->getVehiclesDataForLicence($id, $filters);
    }

    public function getFormData($id)
    {
        return [];
    }

    /**
     * Remove transfer button
     *
     * @param $table Common\Service\Table\TableBuilde
     * @param int $licenceId
     * @return Common\Service\Table\TableBuilde
     */
    public function alterVehicleTable($table, $licenceId)
    {
        $otherLicences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOtherActiveLicences($licenceId);
        if (!count($otherLicences)) {
            $table->removeAction('transfer');
        }
        return $table;
    }
}
