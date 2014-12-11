<?php

/**
 * PSV Licence Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * PSV Licence Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait PsvLicenceControllerTrait
{
    /**
     * We only want to show active vehicles which
     * haven't been marked as removed
     *
     * @param array $licenceVehicle
     * @param array $filters
     * @return boolean
     */

    protected function showVehicle(array $licenceVehicle, array $filters = [])
    {
        return (!empty($licenceVehicle['specifiedDate']) && empty($licenceVehicle['removalDate']));
    }
}
