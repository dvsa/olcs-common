<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;

/**
 * Application People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeople extends AbstractPeople
{
    /**
     * Alter form
     *
     * @param Form $form form
     *
     * @return Form
     */
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeFormAction($form, 'cancel');

        return $form;
    }
}
