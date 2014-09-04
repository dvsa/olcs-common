<?php

/**
 * Licence Generic Vehicle Section Trait
 *
 * Internal/External - Licence - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Licence Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceGenericVehicleSection
{
    /**
     * Holds the section type
     *
     * @var string
     */
    protected $sectionType = 'Licence';

    /**
     * We only want to show active vehicles
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle($licenceVehicle)
    {
        if (empty($licenceVehicle['specifiedDate'])) {
            return false;
        }

        return true;
    }

    /**
     * Request disc
     *
     * @param int $licenceVehicleId
     */
    protected function requestDisc($licenceVehicleId)
    {
        $this->makeRestCall('GoodsDisc', 'POST', array('licenceVehicle' => $licenceVehicleId));
    }
}
