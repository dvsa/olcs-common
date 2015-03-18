<?php

/**
 * Licence Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceBusinessDetails extends AbstractBusinessDetails
{
    public function alterForm($form)
    {
        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        parent::alterForm($form);
    }
}
