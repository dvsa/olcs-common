<?php

/**
 * Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Licence extends AbstractLvaFormService
{
    public function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');
        $this->setPrimaryAction($form, 'save');
    }
}
