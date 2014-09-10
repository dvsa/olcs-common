<?php

/**
 * External Generic Vehicle Section
 *
 * External - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * External Generic Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ExternalGenericVehicleSection
{
    protected $sectionLocation = 'External';

    /**
     * Alter the action form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterActionForm($form)
    {
        $form->get('licence-vehicle')->remove('receivedDate');

        return $this->doAlterActionForm($form);
    }
}
