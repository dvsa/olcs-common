<?php

/**
 * Licence Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Licence Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceLvaAdapter extends AbstractLvaAdapter
{
    public function getIdentifier()
    {
    }

    /**
     * Alter the form based on the LVA rules
     *
     * @param \Zend\Form\Form $form
     */
    public function alterForm(Form $form)
    {
        $form->get('form-actions')->remove('saveAndContinue');
    }
}
