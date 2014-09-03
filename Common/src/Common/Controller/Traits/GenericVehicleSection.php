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
        $saved = parent::actionSave($data);

        if ($action == 'add') {

            if (!isset($saved['id'])) {

                throw new \Exception('Unable to save vehicle');
            }

            $licenceVehicleData = array(
                'licence' => $this->getLicenceId(),
                'dateApplicationReceived' => date('Y-m-d H:i:s'),
                'vehicle' => $saved['id']
            );

            parent::actionSave($licenceVehicleData, 'LicenceVehicle');
        }
    }
}
