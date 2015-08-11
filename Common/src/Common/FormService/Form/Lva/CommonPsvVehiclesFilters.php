<?php

/**
 * Common PSV Vehicles Filter Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Common PSV Vehicles Filter Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonPsvVehiclesFilters extends AbstractFormService
{
    public function getForm()
    {
        return $this->alterForm($this->getFormHelper()->createForm('Lva\PsvVehicleFilter', false));
    }

    protected function alterForm($form)
    {
        $this->getFormHelper()->remove($form, 'vrm');
        $this->getFormHelper()->remove($form, 'specified');
        $this->getFormHelper()->remove($form, 'disc');
        $this->getFormHelper()->remove($form, 'limit');

        return $form;
    }
}
