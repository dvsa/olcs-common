<?php

/**
 * Variation Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Variation extends AbstractLvaFormService
{
    public function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');
        $this->setPrimaryAction($form, 'save');
    }
}
