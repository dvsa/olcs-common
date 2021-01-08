<?php

/**
 * Generic Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Generic Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericVehiclesVehicle extends AbstractFormService
{
    /**
     * Generic form alterations
     *
     * @param \Laminas\Form\Form $form
     * @param array $params
     * @return \Laminas\Form\Form
     */
    public function alterForm($form, $params)
    {
        if ($params['mode'] === 'edit') {
            $this->getFormHelper()->disableElement($form, 'data->vrm');
        }

        if ($params['mode'] === 'edit' || !$params['canAddAnother']) {
            $form->get('form-actions')->remove('addAnother');
        }
    }
}
