<?php

/**
 * PSV Application Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * PSV Application Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait PsvApplicationControllerTrait
{
    /**
     * Whether to display the vehicle
     *
     * @param array $licenceVehicle
     * @param array $filters
     * @return boolean
     */

    protected function showVehicle(array $licenceVehicle, array $filters = [])
    {
        return empty($licenceVehicle['removalDate']);
    }
}
