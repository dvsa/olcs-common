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
        $licence = $this->getController()->params('licence');

        if ($licence !== null) {
            return $licence;
        }

        $application = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($application);
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
