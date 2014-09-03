<?php

/**
 * Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

/**
 * Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericVehicleSection
{
    /**
     * Save vehicle
     *
     * @param array $data
     * @throws \Exception
     */
    protected function saveVehicle($data, $action)
    {
        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        $saved = parent::actionSave($data, 'Vehicle');

        if ($action == 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        parent::actionSave($licenceVehicle, 'LicenceVehicle');
    }
}
