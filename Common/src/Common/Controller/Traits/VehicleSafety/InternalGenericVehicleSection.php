<?php

/**
 * Internal Generic Vehicle Section
 *
 * Internal - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @NOTE Includes shared logic between the INTERNAL vehicle and vehicle-psv sections, both Application and Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Internal Generic Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait InternalGenericVehicleSection
{
    /**
     * Alter the action form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterActionForm($form)
    {
        return $this->doAlterActionForm($form);
    }

    /**
     * Shared logic between internal vehicle sections
     *
     * @param array $data
     * @param string $action
     * @return mixed
     */
    protected function internalActionSave($data, $action)
    {
        if ($action == 'add') {
            $data['licence-vehicle']['specifiedDate'] = date('Y-m-d');
        }

        return $this->doActionSave($data, $action);
    }
}
