<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Common PSV Vehicles Filter Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonPsvVehiclesFilters extends AbstractFormService
{
    /**
     * Get Form
     *
     * @return \Laminas\Form\FormInterface
     */
    public function getForm()
    {
        return $this->alterForm($this->getFormHelper()->createForm('Lva\PsvVehicleFilter', false));
    }

    /**
     * Form
     *
     * @param \Laminas\Form\FormInterface $form Form
     *
     * @return \Laminas\Form\FormInterface
     */
    protected function alterForm($form)
    {
        $this->getFormHelper()->remove($form, 'vrm');
        $this->getFormHelper()->remove($form, 'specified');
        $this->getFormHelper()->remove($form, 'disc');
        $this->getFormHelper()->remove($form, 'limit');

        return $form;
    }
}
