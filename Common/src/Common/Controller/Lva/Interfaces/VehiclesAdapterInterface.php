<?php

/**
 * Vehicles Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Vehicles Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface VehiclesAdapterInterface
{
    /**
     * Get vehicles data for the given resource
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id);
}
