<?php

/**
 * Application People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

/**
 * Application People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeople extends AbstractPeople
{
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        // Always remove these 2 buttons
        $this->removeFormAction($form, 'save');
        $this->removeFormAction($form, 'cancel');

        return $form;
    }
}
