<?php

/**
 * Application Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Application Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvVehiclesVehicle extends AbstractPsvVehiclesVehicle
{
    protected function alterForm($form, $params)
    {
        $dataFieldset = $form->get('licence-vehicle');
        $specifiedDate = $dataFieldset->get('specifiedDate');
        $this->getFormHelper()->disableDateElement($specifiedDate);
        $this->getFormHelper()->disableDateElement($dataFieldset->get('removalDate'));

        $specifiedDate->getYearElement()->setValue('');
        $specifiedDate->getMonthElement()->setValue('');
        $specifiedDate->getDayElement()->setValue('');
        $specifiedDate->getHourElement()->setValue('');
        $specifiedDate->getMinuteElement()->setValue('');

        parent::alterForm($form, $params);

        if ($params['isRemoved']) {
            $form->get('form-actions')->remove('submit');
        }
    }
}
