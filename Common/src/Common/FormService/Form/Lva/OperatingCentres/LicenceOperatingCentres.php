<?php

/**
 * Licence Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentres;

use Zend\Form\Form;

/**
 * Licence Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentres extends AbstractOperatingCentres
{
    protected function alterForm(Form $form, array $params)
    {
        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        parent::alterForm($form, $params);
    }
}
