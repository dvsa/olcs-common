<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Zend\Form\Form;

/**
 * Application Type Of Licence
 */
class ApplicationTypeOfLicence extends AbstractTypeOfLicence
{
    protected function alterForm(Form $form, $params = [])
    {
        parent::alterForm($form, $params);

        $this->getFormServiceLocator()->get('lva-application')->alterForm($form);
    }
}
