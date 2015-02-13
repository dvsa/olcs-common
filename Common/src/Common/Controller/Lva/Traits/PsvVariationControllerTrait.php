<?php

/**
 * Psv Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Psv Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait PsvVariationControllerTrait
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
        return empty($licenceVehicle['removalDate']);
    }
}
