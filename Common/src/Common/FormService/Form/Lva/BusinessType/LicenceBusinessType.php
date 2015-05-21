<?php

/**
 * Licence Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\BusinessType;

use Zend\Form\Form;

/**
 * Licence Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceBusinessType extends AbstractBusinessType
{
    protected function alterForm(Form $form, $params)
    {
        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        parent::alterForm($form, $params);
    }
}
