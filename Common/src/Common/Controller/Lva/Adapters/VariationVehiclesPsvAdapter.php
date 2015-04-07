<?php

/**
 * Variation Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Vehicles Psv Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvAdapter extends AbstractVehiclesPsvAdapter
{
    /**
     * Get vehicles data for the given resource
     *
     * Here we can just wrap the application version
     *
     * @param int $id
     * @return array
     */
    public function getVehiclesData($id)
    {
        return $this->getServiceLocator()->get('ApplicationVehiclesPsvAdapter')->getVehiclesData($id);
    }

    /**
     * Format removed and specified dates if needed
     *
     * @param array $licenceVehicle
     * @return array
     */
    public function maybeFormatRemovedAndSpecifiedDates($licenceVehicle)
    {
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
        unset($data['licence-vehicle']['specifiedDate']);
        return $data;
    }

    public function warnIfAuthorityExceeded($applicationId, $types, $redirecting)
    {
        return $this->getServiceLocator()->get('ApplicationVehiclesPsvAdapter')
            ->warnIfAuthorityExceeded($applicationId, $types, $redirecting);
    }
}
