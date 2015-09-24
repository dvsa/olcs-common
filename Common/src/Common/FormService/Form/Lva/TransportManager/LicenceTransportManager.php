<?php

/**
 * Licence Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\TransportManager;

/**
 * Licence Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTransportManager extends AbstractTransportManager
{
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeFormAction($form, 'saveAndContinue');

        return $form;
    }
}
