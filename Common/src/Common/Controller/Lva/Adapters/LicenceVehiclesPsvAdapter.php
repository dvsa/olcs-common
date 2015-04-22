<?php

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesPsvAdapter extends AbstractVehiclesPsvAdapter
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

    /**
     * Format removed and specified dates if needed
     *
     * @param array $licenceVehicle
     * @return array
     */
    public function maybeFormatRemovedAndSpecifiedDates($licenceVehicle)
    {
        if (isset($licenceVehicle['specifiedDate']) && is_array($licenceVehicle['specifiedDate'])) {
            if (checkdate(
                (int)$licenceVehicle['specifiedDate']['month'],
                (int)$licenceVehicle['specifiedDate']['day'],
                (int)$licenceVehicle['specifiedDate']['year']
            )) {
                $licenceVehicle['specifiedDate'] = sprintf(
                    '%s-%s-%s',
                    $licenceVehicle['specifiedDate']['year'],
                    $licenceVehicle['specifiedDate']['month'],
                    $licenceVehicle['specifiedDate']['day']
                );
            } else {
                $licenceVehicle['specifiedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d');
            }
        }
        if (isset($licenceVehicle['removalDate']) && is_array($licenceVehicle['removalDate'])) {
            if (checkdate(
                (int)$licenceVehicle['removalDate']['month'],
                (int)$licenceVehicle['removalDate']['day'],
                (int)$licenceVehicle['removalDate']['year']
            )) {
                $licenceVehicle['removalDate'] = sprintf(
                    '%s-%s-%s',
                    $licenceVehicle['removalDate']['year'],
                    $licenceVehicle['removalDate']['month'],
                    $licenceVehicle['removalDate']['day']
                );
            } else {
                unset($licenceVehicle['removalDate']);
            }
        }
        return $licenceVehicle;
    }

    /**
     * Unset specified date if needed
     *
     * @param array $data
     * @return array
     */
    public function maybeUnsetSpecifiedDate($data)
    {
        return $data;

    }

    public function warnIfAuthorityExceeded($applicationId, $types, $redirecting)
    {
        // no-op
    }

    /**
     * Remove transfer button
     *
     * @param $table Common\Service\Table\TableBuilde
     * @param int $licenceId
     * @return Common\Service\Table\TableBuilde
     */
    public function alterVehcileTable($table, $licenceId)
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
