<?php

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Licence Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceGenericVehiclesControllerTrait
{
    /**
     * We only want to show active vehicles which
     * haven't been marked as removed
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle(array $licenceVehicle)
    {
        return (!empty($licenceVehicle['specifiedDate']) && empty($licenceVehicle['removalDate']));
    }

    /**
     * Shared logic between licence vehicle sections
     *
     * @param array $data
     * @param string $mode
     * @return mixed
     */
    protected function preSaveVehicle($data, $mode)
    {
        if ($mode === 'add') {
            $data['licence-vehicle']['specifiedDate'] = date('Y-m-d');
        }

        return $data;
    }
}
