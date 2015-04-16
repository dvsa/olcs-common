<?php

/**
 * Vehicles Psv Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Vehicles Psv Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface VehiclesPsvAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id);
}
