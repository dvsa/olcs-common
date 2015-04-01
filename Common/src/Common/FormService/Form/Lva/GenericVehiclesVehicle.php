<?php

/**
 * Generic Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\Form\Elements\Validators\NewVrm;

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
     * @param \Zend\Form\Form $form
     * @param array $params
     * @return \Zend\Form\Form
     */
    public function alterForm($form, $params)
    {
        if ($params['mode'] === 'edit') {
            $this->getFormHelper()->disableElement($form, 'data->vrm');
        }

        if ($params['mode'] === 'add' && $params['isPost']) {

            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validator = new NewVrm();

            $validator->setType(ucwords($params['lva']));
            $validator->setVrms($params['currentVrms']);

            $validators->attach($validator);
        }

        if ($params['mode'] === 'edit' || !$params['canAddAnother']) {
            $form->get('form-actions')->remove('addAnother');
        }
    }
}
