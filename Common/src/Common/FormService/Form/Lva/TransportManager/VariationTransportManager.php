<?php

/**
 * Variation Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\TransportManager;

/**
 * Variation Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTransportManager extends AbstractTransportManager
{
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeFormAction($form, 'saveAndContinue');

        return $form;
    }
}
